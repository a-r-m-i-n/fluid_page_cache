<?php declare(strict_types=1);
namespace T3\FluidPageCache\Fluid\ViewHelpers;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use T3\FluidPageCache\PageCacheManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper analyses during template rendering process, the accessed entities, in variable provider.
 *
 * The node interceptor shipped with fluid_page_cache, wraps this internal view helper around every ObjectAccessor node.
 *
 * @internal
 */
class InterceptorEnricherViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objectPath', 'string', '', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $objectPath = trim($arguments['objectPath'], "'");
        $subject = $renderingContext->getVariableProvider()->getByPath($objectPath);
        if ($subject) {
            PageCacheManager::registerEntity($subject);
        }
        return $renderChildrenClosure();
    }
}
