<?php declare(strict_types=1);
namespace T3\FluidPageCache;

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class Compatibility
{
    /**
     * Checks if the current TYPO3 version is 12.4.0 or greater (by default)
     *
     * @param string $version e.g. 12.4.0
     * @return bool
     */

    public static function isTypo3Version(string $version = '12.4.0'): bool
    {
        $currentVersion = VersionNumberUtility::getNumericTypo3Version();
        return $currentVersion >= VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    public static function getTableNameCachePages(): string
    {
        return 'cache_pages';
    }

    public static function getTableNameCachePagesTags(): string
    {
        return 'cache_pages_tags';
    }

}
