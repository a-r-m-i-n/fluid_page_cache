<?php declare(strict_types = 1);
namespace T3\FluidPageCache\Controller;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use T3\FluidPageCache\Services\PageCacheReport;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BackendModuleController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly CacheManager $cacheManager,
        private readonly PageCacheReport $pageCacheReport,
        private readonly IconFactory $iconFactory,
    ) {
    }

    public function mainAction(): \Psr\Http\Message\ResponseInterface
    {
        /* @var $languageService LanguageService */
        $languageService = $GLOBALS['LANG'];

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $cacheBackendName = $this->pageCacheReport->getPagesCacheBackendName($this->cacheManager);
        $method = 'list' . $cacheBackendName . 'Entries';

        $previewDataAttributes = null;
        if ($this->request->getQueryParams() && isset($this->request->getQueryParams()['id'])) {
            $recordUid = (int) $this->request->getQueryParams()['id'];
            $items = method_exists($this->pageCacheReport, $method) ? $this->pageCacheReport->$method($recordUid) : [];
            $moduleTemplate->assign('recordId', $recordUid);
            $moduleTemplate->assign('identifiers', array_reverse($items));
            $moduleTemplate->assign('pageRow', BackendUtility::getRecord('pages', $recordUid));

            $previewDataAttributes = PreviewUriBuilder::create($recordUid)
                ->withRootLine(BackendUtility::BEgetRootLine($recordUid))
                ->buildDispatcherDataAttributes();
        }

        $moduleTemplate->assign('now', new \DateTime());
        $moduleTemplate->assign('cacheBackendSupported', method_exists($this->pageCacheReport, $method));
        $moduleTemplate->assign('cacheBackendName', $cacheBackendName);
        $moduleTemplate->assign('cacheBackendNameFull', $this->pageCacheReport->getPagesCacheBackendName($this->cacheManager, false));

        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $currentUrl = $this->request->getUri()->__toString();
        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref($currentUrl)
            ->setTitle(
                $languageService->sL(
                    'LLL:EXT:fluid_page_cache/Resources/Private/Language/locallang_mod.xlf:mlang_button_refresh'
                )
            )
            ->setIcon(
                $this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL)
            )
            ->setShowLabelText(true);

        if($previewDataAttributes) {
            $viewButton = $buttonBar->makeLinkButton()
                ->setHref('#')
                ->setDataAttributes($previewDataAttributes)
                ->setTitle(
                    $languageService->sL(
                        'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.showPage'
                    )
                )
                ->setIcon($this->iconFactory->getIcon('actions-view-page', Icon::SIZE_SMALL)
                )
                ->setShowLabelText(true);
            $buttonBar->addButton($viewButton);
        }
        $buttonBar->addButton($refreshButton);

        return $moduleTemplate->renderResponse('PageCacheReport');
    }
}
