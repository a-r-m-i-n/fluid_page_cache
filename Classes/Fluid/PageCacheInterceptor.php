<?php declare(strict_types=1);
namespace T3\FluidPageCache\Fluid;

use T3\FluidPageCache\Utility\CacheUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\Parser\InterceptorInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\EscapingNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\Expression\ExpressionNodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;

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

        if ($node instanceof ObjectAccessorNode) {
            $truncatedObjectPath = strpos('.', $node->getObjectPath()) !== false
                ? substr($node->getObjectPath(), strrpos('.', $node->getObjectPath()))
                : $node->getObjectPath();

            $wrapperNode = new ViewHelperNode($context, 'fluidPageCache', 'interceptorEnricher', [
                'objectPath' => "'" . $truncatedObjectPath . "'"
            ], $parsingState);
            $wrapperNode->addChildNode($node);
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
