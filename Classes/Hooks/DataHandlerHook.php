<?php declare(strict_types=1);
namespace T3\FluidPageCache\Hooks;

use T3\FluidPageCache\Utility\RegistryUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerHook
{
    public function clearCachePostProc(array $params)
    {
        // When all/system/pages caches get cleared
        if (isset($params['cacheCmd'])) {
            RegistryUtility::clear();
        }

        // When record gets updated
        if (isset($params['table']) && isset($params['uid'])) {
            if (RegistryUtility::isEnabled($params['table'])) {
                $cacheTag = $params['table'] . '_' . $params['uid'];

                $cacheTagsToFlush = [$cacheTag];
                /** @var CacheManager $cacheManager */
                $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
                foreach ($cacheTagsToFlush as $cacheTag) {
                    $cacheManager->flushCachesInGroupByTag('pages', $cacheTag);
                }
            }
        }
    }
}
