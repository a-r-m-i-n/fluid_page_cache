<?php
namespace T3\FluidPageCache\XClasses;

use T3\FluidPageCache\Compatibility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * XClass for CmsVariableProvider
 */
class CmsVariableProvider extends \TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider
{
    /**
     * Get a variable by dotted path expression, retrieving the
     * variable from nested arrays/objects one segment at a time.
     * If the second argument is provided, it must be an array of
     * accessor names which can be used to extract each value in
     * the dotted path.
     *
     * @param string $path
     * @param array $accessors
     * @return mixed
     */
    public function getByPath($path, array $accessors = [])
    {
        if (!Compatibility::isTypo3Version()) {
            $path = $this->resolveSubVariableReferences($path);
            $value = ObjectAccess::getPropertyPath($this->variables, $path);

            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($value);
            \T3\FluidPageCache\Utility\CacheUtility::registerEntity($value);

            return parent::getByPath($path, $accessors);
        }
        return parent::getByPath($path, $accessors);
    }
}
