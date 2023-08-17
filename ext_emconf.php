<?php
// phpcs:disable
$EM_CONF[$_EXTKEY] = [
    'title' => 'Fluid Page Cache',
    'description' => 'Creates automatically tags for TYPO3\'s page cache, based on used variables in rendered Fluid templates on current page.',
    'category' => 'be',
    'author' => 'Armin Vieweg',
    'author_email' => 'info@v.ieweg.de',
    'author_company' => '',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'T3\\FluidPageCache\\' => 'Classes/'
        ]
    ]
];
