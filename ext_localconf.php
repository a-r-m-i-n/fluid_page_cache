<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$boot = function() {
    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['fluid_page_cache'] =
            \T3\FluidPageCache\Hooks\DataHandlerHook::class . '->clearCachePostProc';
    }

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Fluid\Core\Variables\CmsVariableProvider::class] = [
        'className' => \T3\FluidPageCache\XClasses\CmsVariableProvider::class
    ];
};
$boot();
unset($boot);
