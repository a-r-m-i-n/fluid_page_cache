<?php declare(strict_types=1);
namespace T3\FluidPageCache\ViewHelpers;

use T3\FluidPageCache\Utility\CacheUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Allows you to add new cache tags from view.
 * You only need to use this view helper, when not assigning entities into your templates.
 *
 * If you use, e.g. such constructs in your template:
 *
 * <f:cObject typoscriptObjectPath="lib.contentElementRendering">{element.uid}</f:cObject>
 *
 *
 * You can register the rendered content element in page cache, like this:
 *
 * <bra:registerCacheTag table="tt_content" uid="{element.uid}" />
 *
 * The view helper has no output.
 */
class RegisterCacheTagViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'Tablename of record to create page cache tag for.', true);
        $this->registerArgument('uid', 'int', 'UID of record to create page cache tag for.', true);
    }

    public function render()
    {
        CacheUtility::registerCacheTag((string) $this->arguments['table'], (int) $this->arguments['uid']);
    }
}
