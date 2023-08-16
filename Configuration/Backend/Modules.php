<?php

use T3\FluidPageCache\Services\ExtensionConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return GeneralUtility::makeInstance(ExtensionConfigurationManager::class)
    ->getBackendModule();
