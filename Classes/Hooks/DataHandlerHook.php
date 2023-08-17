<?php declare(strict_types=1);
namespace T3\FluidPageCache\Hooks;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 *  |     2023 Joel Mai <mai@iwkoeln.de>
 */
use T3\FluidPageCache\PageCacheManager;
use T3\FluidPageCache\Utility\RegistryUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * This hook is called, after a "clear cache" has been performed,
 * which also happens by default, when a record gets updated.
 */
class DataHandlerHook
{
    public function __construct(private readonly CacheManager $cacheManager)
    {
    }

    public function clearCachePostProc(array $params): void
    {
        // When all/system/pages caches get cleared
        if (isset($params['cacheCmd'])) {
            RegistryUtility::clear();
        }

        $cacheTags = [];
        // When record gets updated
        if (isset($params['table'], $params['uid']) && RegistryUtility::isEnabled($params['table'])) {
            $cacheTags[] = PageCacheManager::CACHE_TAG_PREFIX . $params['table'] . '_' . $params['uid'];
        }
        if (isset($params['uid_page'])) {
            $cacheTags[] = PageCacheManager::CACHE_TAG_PREFIX . 'pid_' . $params['uid_page'];
        }

        $this->cacheManager->flushCachesInGroupByTags('pages', $cacheTags);
    }
}
