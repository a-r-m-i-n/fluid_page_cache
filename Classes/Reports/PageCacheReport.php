<?php

namespace T3\FluidPageCache\Reports;

use Doctrine\DBAL\Exception;
use RedisException;
use T3\FluidPageCache\Cache\Backend\CustomRedisBackend;
use T3\FluidPageCache\Cache\Backend\CustomSimpleFileBackend;
use T3\FluidPageCache\PageCacheManager;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageCacheReport
{

    /**
     * @throws \TYPO3\CMS\Core\Cache\Exception
     */
    public function listSimpleFileBackendEntries($cacheManager, $pageUid): array
    {
        $options = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pages']['options'] ?? [];

        /** @var CustomSimpleFileBackend $backend */
        $backend = GeneralUtility::makeInstance(CustomSimpleFileBackend::class, '', $options);
        $backend->setCache($cacheManager->getCache('pages'));

        $keys = $backend->all();
        $result = [];
        foreach ($keys as $key) {
            $row = $this->getCacheKeyInfo($backend, $key, $pageUid);
            if ($row) {
                $result[$key] = $row;
            }
        }
        return $result;
    }

    /**
     * @throws RedisException
     */
    public function listRedisBackendEntries($cacheManager, $pageUid): array
    {
        $options = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pages']['options'];

        /** @var CustomRedisBackend $backend */
        $backend = GeneralUtility::makeInstance(CustomRedisBackend::class, '', $options);
        $backend->setCache($cacheManager->getCache('pages'));

        $keys = $backend->all();
        $result = [];
        foreach ($keys as $key) {
            $row = $this->getCacheKeyInfo($backend, $key, $pageUid);
            if ($row) {
                $result[$key] = $row;
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function listTypo3DatabaseBackendEntries($pageUid): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $queryBuilder = $connectionPool->getQueryBuilderForTable('cache_pages_tags');
        $cacheTagRows = $queryBuilder
            ->select('*')
            ->from('cache_pages_tags')
            ->where('tag = "pageId_' . $pageUid . '"')
            ->executeQuery()
            ->fetchAllAssociative() ?? [];

        $identifiers = [];
        foreach ($cacheTagRows as $cacheTagRow) {
            $queryBuilder = $connectionPool->getQueryBuilderForTable('cache_pages');
            $cacheRow = $queryBuilder
                ->select('id', 'identifier', 'expires')
                ->from('cache_pages')
                ->where('identifier = "' . $cacheTagRow['identifier'] . '"')
                ->executeQuery()
                ->fetchAssociative();

            $queryBuilder = $connectionPool->getQueryBuilderForTable('cache_pages_tags');
            $tagRows = $queryBuilder
                ->select('*')
                ->from('cache_pages_tags')
                ->where('identifier = "' . $cacheTagRow['identifier'] . '"')
                ->executeQuery()
                ->fetchAllAssociative();

            $tags = [];
            foreach ($tagRows as $tagRow) {
                $tags[] = $this->createTagRowByTagName($tagRow['tag']);
            }

            $identifiers[$cacheTagRow['identifier']] = ['tags' => $tags, 'expires' => $cacheRow['expires']];
        }
        return $identifiers;
    }

    public function resolveRecordTitle(string $table, int $uid): string
    {
        $labelField = $GLOBALS['TCA'][$table]['ctrl']['label'];
        if (!$labelField) {
            return '';
        }
        $row = BackendUtility::getRecord($table, $uid);
        return (string) $row[$labelField];
    }

    public function createTagRowByTagName(string $tagName): array
    {
        $table = $uid = null;
        $tag = $tagName;
        if (preg_match('/^' . PageCacheManager::CACHE_TAG_PREFIX . '(.*)_(\d*)$/i', $tag, $matches)) {
            $table = $matches[1];
            $uid = (int)$matches[2];

            if ($table === 'pid') {
                $table = 'pages';
            }
        } elseif (strpos($tag, 'pageId_') === 0) {
            $table = 'pages';
            $uid = (int)substr($tag, strlen('pageId_'));
        }
        return [
            'tag' => $tag,
            'table' => $table,
            'uid' => $uid,
            'row' => ($table && $uid) ? BackendUtility::getRecord($table, $uid) : null,
            'title' => ($table && $uid) ? $this->resolveRecordTitle($table, $uid) : null
        ];
    }

    public function getPagesCacheBackendName($cacheManager): string
    {
        $cache = $cacheManager->getCache('pages');
        $backend = get_class($cache->getBackend());
        $backend = explode('\\', $backend);

        return end($backend);
    }

    public function getCacheKeyInfo(AbstractBackend $backend, $keySanitized, $pageUid): ?array
    {
        $info = $backend->get($keySanitized);
        $info = unserialize($info, ['allowed_classes' => false]);

        if ($info['page_id'] !== $pageUid) {
            return null;
        }

        $tags = [];
        foreach ($info['cacheTags'] ?? [] as $tagName) {
            $tags[] = $this->createTagRowByTagName($tagName);
        }

        return [
            'tags' => $tags,
            'expires' => $info['expires'],
        ];
    }
}
