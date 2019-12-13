<?php declare(strict_types=1);
namespace T3\FluidPageCache\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

class CacheUtility
{
    /**
     * @var DataMapper
     */
    private static $dataMapper;

    /**
     * @var array
     */
    private static $addedCacheTags = [];

    /**
     * Detect table name and uid of given Extbase entity, and register cache tag for entity to current page.
     * This method only works in Frontend context and when $GLOBALS['TSFE'] is available.
     *
     * @param AbstractEntity|mixed $entity
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public static function registerEntity($entity)
    {
        if (!$entity || !isset($GLOBALS['TSFE']) || TYPO3_MODE !== 'FE') {
            return;
        }
        if (is_iterable($entity)) {
            foreach ($entity as $singleEntity) {
                static::processSingleEntity($singleEntity);
            }
        } else {
            static::processSingleEntity($entity);
        }
    }

    /**
     * @param AbstractEntity|mixed $entity
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    private static function processSingleEntity($entity)
    {
        if ($entity instanceof AbstractEntity) {
            $tableName = static::getDatabaseTableNameOfEntity($entity);
            static::registerCacheTag($tableName, $entity->getUid());
        }
    }

    /**
     * Builds and registers a cache tag, by given table name and uid.
     *
     * @param string $table
     * @param int $uid
     * @return void
     */
    public static function registerCacheTag(string $table, int $uid): void
    {
        $cacheTag = $table . '_' . $uid;
        if (!in_array($cacheTag, static::$addedCacheTags, true)) {
            // Adding to page cache
            $GLOBALS['TSFE']->addCacheTags([$cacheTag]);
            RegistryUtility::enable($table);
            static::$addedCacheTags[] = $cacheTag;
        }
    }

    /**
     * Returns database table name of given Extbase entity
     *
     * @param AbstractEntity $entity Extbase entity
     * @return string Database table name of given entity
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected static function getDatabaseTableNameOfEntity(AbstractEntity $entity): string
    {
        if (!static::$dataMapper) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$dataMapper = $objectManager->get(DataMapper::class);
        }
        return static::$dataMapper->getDataMap(get_class($entity))->getTableName();
    }
}
