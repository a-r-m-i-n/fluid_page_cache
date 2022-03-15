<?php declare(strict_types=1);
namespace T3\FluidPageCache;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2022 Armin Vieweg <info@v.ieweg.de>
 */
use T3\FluidPageCache\Utility\RegistryUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * The page cache manager
 * Creates cache tags for Extbase entities on current page (FE) in TYPO3's page cache.
 *
 * You can utilized the public static methods of this class, to register own cache tags, manually.
 */
class PageCacheManager
{
    /**
     * All cache tags, created by fluid_page_cache got this prefix
     */
    public const CACHE_TAG_PREFIX = 'fpc_';

    /**
     * @var DataMapper
     */
    private static $dataMapper;

    /**
     * @var FileRepository
     */
    private static $fileRepository;

    /**
     * @var array
     */
    private static $addedCacheTags = [];

    /**
     * Detect table name and uid of given Extbase entity, and register cache tag for entity to current page.
     * This method only works in Frontend context and when $GLOBALS['TSFE'] is available.
     *
     * @param AbstractDomainObject|mixed $entity
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @api
     */
    public static function registerEntity($entity)
    {
        if (isset($GLOBALS['TSFE']) &&
            TYPO3_MODE === 'FE' &&
            $entity &&
            $entity instanceof AbstractDomainObject &&
            $entity->getUid()
        ) {
            $tableName = static::getDatabaseTableNameOfEntity($entity);
            static::registerCacheTag($tableName, $entity->getUid(), $entity->getPid());
        }
    }

    /**
     * Builds and registers a cache tag, by given table name and uid.
     *
     * @param string $table
     * @param int $uid
     * @param int $pid only applies when $GLOBALS['TSFE']->page['tx_fluidpagecache_pid_cache_tag'] is set
     * @api
     */
    public static function registerCacheTag(string $table, int $uid, int $pid = 0): void
    {
        if (!isset($GLOBALS['TSFE']) || TYPO3_MODE !== 'FE') {
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
                if (!static::$fileRepository) {
                    $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                    static::$fileRepository = $objectManager->get(FileRepository::class);
                }
                $reference = static::$fileRepository->findFileReferenceByUid($uid);
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
     * @param AbstractDomainObject $entity Extbase entity
     * @return string Database table name of given entity
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    private static function getDatabaseTableNameOfEntity(AbstractDomainObject $entity): string
    {
        if (!static::$dataMapper) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$dataMapper = $objectManager->get(DataMapper::class);
        }
        return static::$dataMapper->getDataMap(get_class($entity))->getTableName();
    }
}
