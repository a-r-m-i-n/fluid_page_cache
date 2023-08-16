<?php

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */

// phpcs:disable
$boot = function () {

    // Register DataHandler hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['fluid_page_cache'] =
            \T3\FluidPageCache\Hooks\DataHandlerHook::class . '->clearCachePostProc';

    // Register Fluid interceptor
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['interceptors']['fluid_page_cache'] = \T3\FluidPageCache\Fluid\PageCacheInterceptor::class;

};
$boot();
unset($boot);
