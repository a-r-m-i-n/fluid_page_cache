<?php declare(strict_types=1);
namespace T3\FluidPageCache;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use T3\FluidPageCache\Utility\RegistryUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * The page cache manager
 * Creates cache tags for Extbase entities on current page (FE) in TYPO3's page cache.
 *
 * You can utilize the public static methods of this class, to register own cache tags, manually.
 */
class PageCacheManager
{
    /**
     * All cache tags, created by fluid_page_cache got this prefix
     */
    public const CACHE_TAG_PREFIX = 'fpc_';
    private FileRepository $fileRepository;
    private DataMapper $dataMapper;
    private static array $addedCacheTags = [];

    public function __construct(FileRepository $fileRepository, DataMapper $dataMapper)
    {
        $this->fileRepository = $fileRepository;
        $this->dataMapper = $dataMapper;
    }

    /**
     * Detect table name and uid of given Extbase entity, and register cache tag for entity to current page.
     * This method only works in Frontend context and when $GLOBALS['TSFE'] is available.
     *
     * @param AbstractDomainObject|mixed $entity
     * @throws Exception
     * @api
     */
    public function registerEntity(mixed $entity): void
    {
        if (isset($GLOBALS['TSFE']) &&
            static::isFrontend() &&
            $entity &&
            $entity instanceof AbstractDomainObject &&
            $entity->getUid()
        ) {
            $tableName = $this->getDatabaseTableNameOfEntity($entity);
            $this->registerCacheTag($tableName, $entity->getUid(), $entity->getPid());
        }
    }

    /**
     * Builds and registers a cache tag, by given table name and uid.
     *
     * @api
     */
    public function registerCacheTag(string $table, int $uid, int $pid = 0): void
    {
        if (!isset($GLOBALS['TSFE']) || !static::isFrontend()) {
            return;
        }
        $cacheTagUid = self::CACHE_TAG_PREFIX . $table . '_' . $uid;
        if (!in_array($cacheTagUid, static::$addedCacheTags, true)) {
            $cacheTags = [$cacheTagUid];

            $cacheTagPid = self::CACHE_TAG_PREFIX . 'pid_' . $pid;
            if ($GLOBALS['TSFE']->page['tx_fluidpagecache_pid_cache_tag'] &&
                !in_array($cacheTagPid, static::$addedCacheTags, true)
            ) {
                $cacheTags[] = $cacheTagPid;
            }

            // Follow sys_file_references to related sys_file
            if ($table === 'sys_file_reference') {
                $reference = $this->fileRepository->findFileReferenceByUid($uid);
                if ($reference) {
                    $cacheTags[] = self::CACHE_TAG_PREFIX . 'sys_file_' . $reference->getOriginalFile()->getUid();
                }
            }

            // Adding to page cache
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
            RegistryUtility::enable($table);
            static::$addedCacheTags = array_merge(static::$addedCacheTags, $cacheTags);
        }
    }

    /**
     * Returns database table name of given Extbase entity
     *
     * @throws Exception
     */
    private function getDatabaseTableNameOfEntity(AbstractDomainObject $entity): string
    {
        return $this->dataMapper->getDataMap(get_class($entity))->getTableName();
    }

    private static function isFrontend(): bool
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }
}
