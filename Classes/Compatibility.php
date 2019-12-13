<?php
namespace T3\FluidPageCache;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class Compatibility
{
    /**
     * Checks if current TYPO3 version is 9.0.0 or greater (by default)
     *
     * @param string $version e.g. 9.0.0
     * @return bool True when TYPO3 version is equal or greater, than given version number
     */
    public static function isTypo3Version($version = '9.0.0') : bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >=
            VersionNumberUtility::convertVersionNumberToInteger($version);
    }

    /**
     * Returns the URL to a given module
     *
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     * @throws RouteNotFoundException
     * @see DCE Extension
     */
    public static function getModuleUrl($moduleName, $urlParameters = []) : string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        try {
            $uri = $uriBuilder->buildUriFromRoute($moduleName, $urlParameters);
        } catch (RouteNotFoundException $e) {
            $uri = static::isTypo3Version()
                ? $uriBuilder->buildUriFromRoutePath($moduleName, $urlParameters)
                : $uriBuilder->buildUriFromModule($moduleName, $urlParameters);
        }
        return (string) $uri;
    }
}
