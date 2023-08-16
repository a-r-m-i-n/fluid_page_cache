<?php

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */

$columns = [
    'tx_fluidpagecache_pid_cache_tag' => [
        'label' => 'LLL:EXT:fluid_page_cache/Resources/Private/Language/locallang_db.xml:tx_fluidpagecache_pid_cache_tag',
        'config' => [
            'type' => 'check',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'caching',
    'tx_fluidpagecache_pid_cache_tag',
    'after: cache_timeout'
);
