<?php declare(strict_types=1);
namespace T3\FluidPageCache\Reports;

use TYPO3\CMS\Backend\Module\AbstractFunctionModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PageCacheReport extends AbstractFunctionModule
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * Main method of modfuncreport
     *
     * @return string Module content
     */
    public function main()
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getTemplatePaths()->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:fluid_page_cache/Resources/Private/Templates/PageCacheReport.html')
        );

        $id = (int) (GeneralUtility::_GET('id') ?? 0);
        $view->assign('id', $id);

        if ($id) {
            $view->assign('identifiers', $this->getCacheIdentifiersByPageUid($id));
        }
        return $view->render();
    }



    protected function getCacheIdentifiersByPageUid(int $pageUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('cf_cache_pages_tags');
        $cacheTagRows = $queryBuilder
            ->select('*')
            ->from('cf_cache_pages_tags')
            ->where('tag = "pageId_' . $pageUid . '"')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $identifiers = [];
        foreach ($cacheTagRows as $cacheTagRow) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('cf_cache_pages_tags');
            $tagRows = $queryBuilder
                ->select('*')
                ->from('cf_cache_pages_tags')
                ->where('identifier = "' . $cacheTagRow['identifier'] . '"')
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            $tags = [];
            foreach ($tagRows as $tagRow) {
                $table = $uid = null;
                $tag = $tagRow['tag'];
                if (preg_match('/^(.*)_(\d*)$/i', $tag, $matches)) {
                    $table = $matches[1];
                    if ($table === 'pageId') {
                        $table = 'pages';
                    }
                    $uid = (int) $matches[2];
                }
                $tags[] = [
                    'tag' => $tag,
                    'table' => $table,
                    'uid' => $uid,
                    'row' => ($table && $uid) ? BackendUtility::getRecord($table, $uid) : null,
                    'title' => ($table && $uid) ? $this->resolveRecordTitle($table, $uid) : null
                ];
            }
            $identifiers[$cacheTagRow['identifier']] = $tags;
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
}
