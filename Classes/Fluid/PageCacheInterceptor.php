<?php declare(strict_types=1);
namespace T3\FluidPageCache\Fluid;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2020 Armin Vieweg <armin@v.ieweg.de>
 */
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\Parser\InterceptorInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\EscapingNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;

/**
 * This node interceptor for Fluid template engine, wraps every ObjectAccessorNode with
 * an internal view helper, which identifies the accessed entities in variable provider.
 */
class PageCacheInterceptor implements InterceptorInterface
{

    /**
     * @param NodeInterface $node
     * @param int $interceptorPosition
     * @param ParsingState $parsingState
     * @return NodeInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function process(NodeInterface $node, $interceptorPosition, ParsingState $parsingState)
    {
        $view = new StandaloneView();
        $context = new RenderingContext($view);
        $context->getViewHelperResolver()->addNamespace('fluidPageCache', 'T3\\FluidPageCache\\Fluid\\ViewHelpers');

        $escapingNode = null;
        if ($node instanceof EscapingNode) {
            $escapingNode = $node;
            $node = $node->getNode();
        }

        if ($node instanceof ObjectAccessorNode) {
            $truncatedObjectPath = strpos($node->getObjectPath(), '.') !== false
                ? substr($node->getObjectPath(), 0, strrpos($node->getObjectPath() ,'.'))
                : $node->getObjectPath();

            $wrapperNode = new ViewHelperNode($context, 'fluidPageCache', 'interceptorEnricher', [
                'objectPath' => "'" . $truncatedObjectPath . "'"
            ], $parsingState);
            $wrapperNode->addChildNode($node);
            if ($escapingNode) {
                return new EscapingNode($wrapperNode);
            }
            return $wrapperNode;
        }
        return $node;
    }

    /**
     * @inheritDoc
     */
    public function getInterceptionPoints()
    {
        return [
            InterceptorInterface::INTERCEPT_OBJECTACCESSOR,
        ];
    }
}
