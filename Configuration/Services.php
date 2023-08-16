<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
        ->get('fluid_page_cache');

    if ($extensionConfiguration['enableStandaloneBackendModule'] ?? false) {
        $containerBuilder->setParameter(
            'fluid_page_cache.standaloneBackendModule',
            $extensionConfiguration['enableStandaloneBackendModule']
        );
    }
};
