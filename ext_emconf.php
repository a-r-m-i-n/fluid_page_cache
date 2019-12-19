<?php
// phpcs:disable
$EM_CONF[$_EXTKEY] = [
    'title' => 'Fluid Page Cache',
    'description' => 'Creates automatically tags for TYPO3\'s page cache, based on used variables in rendered Fluid templates on current page.',
    'category' => 'be',
    'author' => 'Armin Vieweg',
    'author_email' => 'armin@v.ieweg.de',
    'state' => 'stable',
    'author_company' => 'v.ieweg Webentwicklung',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99'
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
