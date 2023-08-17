<?php

use T3\FluidPageCache\Controller\BackendModuleController;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/** @var ExtensionConfiguration $extensionConfiguration */
$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

/** @var array $fluidPageCacheConfig */
$fluidPageCacheConfig = $extensionConfiguration->get('fluid_page_cache') ?? [];

if (!($fluidPageCacheConfig['enableStandaloneBackendModule'] ?? false)) {
    return [];
}
return [
    'tools_fluidpagecachereport' => [
        'path' => '/module/tools/fluid-page-cache',
        'parent' => 'tools',
        'access' => 'admin',
        'iconIdentifier' => 'module-fluid_page_cache',
        'labels' => [
            'title' => 'LLL:EXT:fluid_page_cache/Resources/Private/Language/locallang_mod.xlf:mlang_tabs_tab',
            'shortDescription' => 'LLL:EXT:fluid_page_cache/Resources/Private/Language/locallang_mod.xlf:mlang_labels_tablabel',
            'description' => 'LLL:EXT:fluid_page_cache/Resources/Private/Language/locallang_mod.xlf:mlang_labels_tabdescr',
        ],
        'extensionName' => 'FluidPageCache',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'controllerActions' => [
            BackendModuleController::class => [
                'main'
            ],
        ],
    ],
];
