<?php declare(strict_types=1);
namespace T3\FluidPageCache\ViewHelpers\Be;

use T3\FluidPageCache\Compatibility;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper returns a link to module in TYPO3 backend
 *
 * @see DCE Extension
 */
class ModuleLinkViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('module', 'string', 'Name of module');
        $this->registerArgument('parameter', 'string', 'Query string');
    }

    /**
     * Resolve user name from backend user id.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string Created module link
     * @throws RouteNotFoundException
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $parameters = GeneralUtility::explodeUrl2Array($arguments['parameter']);
        return Compatibility::getModuleUrl($arguments['module'], $parameters);
    }
}