<?php

namespace T3\FluidPageCache\Controller;

use T3\FluidPageCache\Reports\PageCacheReport;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BackendModuleController extends ActionController
{
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
    ) {
    }

    public function mainAction(): \Psr\Http\Message\ResponseInterface
    {
        /* @var $languageService LanguageService */
        $languageService = $GLOBALS['LANG'];

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $pageReport = GeneralUtility::makeInstance(PageCacheReport::class);

        $cacheBackendName = $pageReport->getPagesCacheBackendName($cacheManager);
        $method = 'list' . $cacheBackendName . 'Entries';

        $previewDataAttributes = null;
        if ($this->request->getQueryParams() && isset($this->request->getQueryParams()['id'])) {
            $recordUid = (int) $this->request->getQueryParams()['id'];
            $items = method_exists($pageReport, $method) ? $pageReport->$method($recordUid) : [];
            $moduleTemplate->assign('recordId', $recordUid);
            $moduleTemplate->assign('identifiers', array_reverse($items));
            $moduleTemplate->assign('pageRow', BackendUtility::getRecord('pages', $recordUid));

            $previewDataAttributes = PreviewUriBuilder::create($recordUid)
                ->withRootLine(BackendUtility::BEgetRootLine($recordUid))
                ->buildDispatcherDataAttributes();
        }

        $moduleTemplate->assign('now', new \DateTime());
        $moduleTemplate->assign('cacheBackendSupported', method_exists($pageReport, $method));
        $moduleTemplate->assign('cacheBackendName', $cacheBackendName);

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
                GeneralUtility::makeInstance(IconFactory::class)
                    ->getIcon('actions-refresh', Icon::SIZE_SMALL)
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
                ->setIcon(GeneralUtility::makeInstance(IconFactory::class)
                    ->getIcon('actions-view-page', Icon::SIZE_SMALL)
                )
                ->setShowLabelText(true);
            $buttonBar->addButton($viewButton);
        }
        $buttonBar->addButton($refreshButton);

        return $moduleTemplate->renderResponse('PageCacheReport');
    }
}
