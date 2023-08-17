<?php declare(strict_types=1);
namespace T3\FluidPageCache\ViewHelpers\Be;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use Closure;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
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
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('module', 'string', 'Name of module');
        $this->registerArgument('parameter', 'string', 'Query string');
    }

    /**
     * Resolve user name from backend user id.
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $parameters = GeneralUtility::explodeUrl2Array($arguments['parameter']);
        return static::getModuleUrl($arguments['module'], $parameters);
    }

    /**
     * Returns the URL to a given module
     */
    protected static function getModuleUrl(string $moduleName, array $urlParameters = []) : string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        try {
            $uri = $uriBuilder->buildUriFromRoute($moduleName, $urlParameters);
        } catch (RouteNotFoundException $e) {
            $uri = $uriBuilder->buildUriFromRoutePath($moduleName, $urlParameters);
        }
        return (string) $uri;
    }
}
