<?php declare(strict_types=1);
namespace T3\FluidPageCache\Fluid\ViewHelpers;

use T3\FluidPageCache\Utility\CacheUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class InterceptorEnricherViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objectPath', 'string', '', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $objectPath = trim($arguments['objectPath'], "'");
        $subject = $renderingContext->getVariableProvider()->getByPath($objectPath);
        CacheUtility::registerEntity($subject);
        return $renderChildrenClosure();
    }
}
