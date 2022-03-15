<?php declare(strict_types=1);
namespace T3\FluidPageCache\Reports;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2020 Armin Vieweg <armin@v.ieweg.de>
 */
use T3\FluidPageCache\Cache\Backend\CustomRedisBackend;
use T3\FluidPageCache\Cache\Backend\CustomSimpleFileBackend;
use T3\FluidPageCache\PageCacheManager;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Provides entry for Info module
 */
class PageCacheReport
{
    private int $id;
    protected ConnectionPool $connectionPool;
    private CacheManager $cacheManager;

    public function __construct()
    {
        $this->id = (int) (GeneralUtility::_GET('id') ?? 0);
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->cacheManager = GeneralUtility::makeInstance(CacheManager::class);
    }

    /**
     * Main method of modfuncreport
     *
     * @return string Module content
     * @throws \Exception
     */
    public function main()
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getTemplatePaths()->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:fluid_page_cache/Resources/Private/Templates/PageCacheReport.html')
        );

        $cacheBackendName = $this->getPagesCacheBackendName();
        $method = 'list' . $cacheBackendName . 'Entries';
        $items = method_exists($this, $method) ? $this->$method($this->id) : [];

        $view->assign('now', new \DateTime());
        $view->assign('id', $this->id);
        $view->assign('pageRow', BackendUtility::getRecord('pages', $this->id));
        $view->assign('cacheBackendSupported', method_exists($this, $method));
        $view->assign('cacheBackendName', $cacheBackendName);

        if ($this->id) {
            $view->assign('identifiers', array_reverse($items));

        }
        return $view->render();
    }

    protected function listSimpleFileBackendEntries(): array
    {
        $options = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pages']['options'] ?? [];

        /** @var CustomSimpleFileBackend $backend */
        $backend = GeneralUtility::makeInstance(CustomSimpleFileBackend::class, '', $options);
        $backend->setCache($this->cacheManager->getCache('pages'));

        $keys = $backend->all();
        $result = [];
        foreach ($keys as $key) {
            $row = $this->getCacheKeyInfo($backend, $key);
            if ($row) {
                $result[$key] = $row;
            }
        }
        return $result;
    }

    protected function listRedisBackendEntries(): array
    {
        $options = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['pages']['options'];

        /** @var CustomRedisBackend $backend */
        $backend = GeneralUtility::makeInstance(CustomRedisBackend::class, '', $options);
        $backend->setCache($this->cacheManager->getCache('pages'));

        $keys = $backend->all();
        $result = [];
        foreach ($keys as $key) {
            $row = $this->getCacheKeyInfo($backend, $key);
            if ($row) {
                $result[$key] = $row;
            }
        }

        return $result;
    }

    protected function listTypo3DatabaseBackendEntries(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('cache_pages_tags');
        $cacheTagRows = $queryBuilder
            ->select('*')
            ->from('cache_pages_tags')
            ->where('tag = "pageId_' . $this->id . '"')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $identifiers = [];
        foreach ($cacheTagRows as $cacheTagRow) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('cache_pages');
            $cacheRow = $queryBuilder
                ->select('*')
                ->from('cache_pages')
                ->where('identifier = "' . $cacheTagRow['identifier'] . '"')
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);

            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('cache_pages_tags');
            $tagRows = $queryBuilder
                ->select('*')
                ->from('cache_pages_tags')
                ->where('identifier = "' . $cacheTagRow['identifier'] . '"')
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            $tags = [];
            foreach ($tagRows as $tagRow) {
                $tags[] = $this->createTagRowByTagName($tagRow['tag']);
            }
            $identifiers[$cacheTagRow['identifier']] = ['tags' => $tags, 'expires' => $cacheRow['expires']];
        }
        return $identifiers;
    }

    protected function resolveRecordTitle(string $table, int $uid): string
    {
        $labelField = $GLOBALS['TCA'][$table]['ctrl']['label'];
        if (!$labelField) {
            return '';
        }
        $row = BackendUtility::getRecord($table, $uid);
        return (string) $row[$labelField];
    }

    protected function createTagRowByTagName(string $tagName): array
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

    protected function getPagesCacheBackendName(): string
    {
        $cache = $this->cacheManager->getCache('pages');
        $backend = get_class($cache->getBackend());
        $backend = explode('\\', $backend);

        return end($backend);
    }

    protected function getCacheKeyInfo(AbstractBackend $backend, $keySanitized): ?array
    {
        $info = $backend->get($keySanitized);
        $info = unserialize($info, ['allowed_classes' => false]);

        if ($info['page_id'] !== $this->id) {
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
