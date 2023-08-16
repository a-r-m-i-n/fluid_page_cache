<?php

namespace T3\FluidPageCache\Services;

use T3\FluidPageCache\Controller\BackendModuleController;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtensionConfigurationManager
{
    private mixed $fpcConfiguration;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration
    )
    {
        $this->fpcConfiguration = $this->extensionConfiguration->get('fluid_page_cache');
    }

    public function getBackendModule(): array {
        if ($this->fpcConfiguration['enableStandaloneBackendModule'] ?? false) {
            return [
                'tools_pagecachereport' => [
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
        }

        return [];
    }
}
