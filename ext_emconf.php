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
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.0.99',
            'typo3' => '10.4.6-11.5.99'
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
