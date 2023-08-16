<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    // Icon identifier
    'module-fluid_page_cache' => [
        // Icon provider class
        'provider' => SvgIconProvider::class,
        // The source SVG for the SvgIconProvider
        'source' => 'EXT:fluid_page_cache/Resources/Public/Icons/module-fluid_page_cache.svg',
    ],
];
