<?php declare(strict_types=1);
namespace T3\FluidPageCache\Utility;

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RegistryUtility
{
    /**
     * @var Registry
     */
    private static $registry;


    /**
     * @param string $table
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
     * @param string $table
     * @return bool
     */
    public static function isEnabled(string $table): bool
    {
        static::initializeRegistry();
        return in_array($table, static::getEnabledTables(), true);
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

    protected static function getEnabledTables(): array
    {
        $enabledTables = static::$registry->get('fluid_page_cache', 'enabledTables') ?? [];
        if ($enabledTables) {
            $enabledTables = GeneralUtility::trimExplode(',', $enabledTables);
        }
        return $enabledTables;
    }


    protected static function initializeRegistry()
    {
        if (!static::$registry) {
            static::$registry = GeneralUtility::makeInstance(Registry::class);
        }
    }
}
