<?php declare(strict_types=1);
namespace T3\FluidPageCache\Hooks;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2020 Armin Vieweg <armin@v.ieweg.de>
 */
use T3\FluidPageCache\PageCacheManager;
use T3\FluidPageCache\Utility\RegistryUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This hook is called, after a "clear cache" has been performed,
 * which also happens by default, when a record gets updated.
 */
class DataHandlerHook
{
    public function clearCachePostProc(array $params)
    {
        // When all/system/pages caches get cleared
        if (isset($params['cacheCmd'])) {
            RegistryUtility::clear();
        }

        // When record gets updated
        if (isset($params['table'], $params['uid']) && RegistryUtility::isEnabled($params['table'])) {
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $cacheManager->flushCachesInGroupByTag(
                'pages',
                PageCacheManager::CACHE_TAG_PREFIX . $params['table'] . '_' . $params['uid']
            );
        }
    }
}
