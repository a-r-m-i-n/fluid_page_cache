<?php declare(strict_types=1);
namespace T3\FluidPageCache\Utility;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2022 Armin Vieweg <info@v.ieweg.de>
 */
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper class for sys_registry access
 * Used to enable database tables to get cleared in DataHandlerHook.
 */
class RegistryUtility
{
    /**
     * @var Registry
     */
    private static $registry;

    /**
     * Enables table to get cleared during cache clearing
     *
     * @param string $table
     * @return void
     */
    public static function enable(string $table): void
    {
        static::initializeRegistry();
        if (!static::isEnabled($table)) {
            $enabledTables = static::getEnabledTables();
            $enabledTables[] = $table;
            static::$registry->set('fluid_page_cache', 'enabledTables', implode(',', $enabledTables));
        }
    }

    /**
     * Checks if given table is enabled
     *
     * @param string $table
     * @return bool
     */
    public static function isEnabled(string $table): bool
    {
        static::initializeRegistry();
        return in_array($table, static::getEnabledTables(), true);
    }

    /**
     * Returns all enabled pages
     *
     * @return array
     */
    protected static function getEnabledTables(): array
    {
        $enabledTables = static::$registry->get('fluid_page_cache', 'enabledTables') ?? [];
        if ($enabledTables) {
            $enabledTables = GeneralUtility::trimExplode(',', $enabledTables);
        }
        return $enabledTables;
    }

    /**
     * Clears the list of enabled tables.
     * This method is called, when all caches get cleared (pages/system/all)
     *
     * @see \T3\FluidPageCache\Hooks\DataHandlerHook::clearCachePostProc
     */
    public static function clear(): void
    {
        static::initializeRegistry();
        static::$registry->remove('fluid_page_cache', 'enabledTables');
    }

    /**
     * Used in public methods, to initialize the Registry (sys_registry)
     */
    protected static function initializeRegistry(): void
    {
        if (!static::$registry) {
            static::$registry = GeneralUtility::makeInstance(Registry::class);
        }
    }
}
