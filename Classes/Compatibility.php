<?php declare(strict_types=1);
namespace T3\FluidPageCache;

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class Compatibility
{
    /**
     * Checks if current TYPO3 version is 10.0.0 or greater (by default)
     *
     * @param string $version e.g. 10.0.0
     * @return bool
     */
    public static function isTypo3Version($version = '10.0.0') : bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >=
            VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    public static function getTableNameCachePages(): string
    {
        if (!self::isTypo3Version()) {
            return 'cf_cache_pages';
        }
        return 'cache_pages';
    }

    public static function getTableNameCachePagesTags(): string
    {
        if (!self::isTypo3Version()) {
            return 'cf_cache_pages_tags';
        }
        return 'cache_pages_tags';
    }

}
