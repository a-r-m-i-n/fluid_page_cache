<?php
// phpcs:disable
$EM_CONF[$_EXTKEY] = [
    'title' => 'Fluid Page Cache',
    'description' => 'Creates automatically tags for TYPO3\'s page cache, based on used variables in rendered Fluid templates on current page.',
    'category' => 'be',
    'author' => 'Armin Vieweg',
    'author_email' => 'info@v.ieweg.de',
    'state' => 'stable',
    'author_company' => 'v.ieweg Webentwicklung',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'php' => '>=8.1.0',
            'typo3' => '>=12.4.0'
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
