<?php declare(strict_types=1);
namespace T3\FluidPageCache\ViewHelpers;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use T3\FluidPageCache\PageCacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
 * {namespace fpc=T3\FluidPageCache\ViewHelpers}
 * <fpc:registerCacheTag table="tt_content" uid="{element.uid}" pid="{element.pid}" />
 *
 * The view helper has no output.
 */
class RegisterCacheTagViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'Tablename of record to create page cache tag for.', true);
        $this->registerArgument('uid', 'int', 'UID of record to create page cache tag for.', true);
        $this->registerArgument('pid', 'int', 'PID of record to create page cache tag for.', true);
    }

    public function render(): void
    {
        GeneralUtility::makeInstance(PageCacheManager::class)->registerCacheTag(
            (string) $this->arguments['table'],
            (int) $this->arguments['uid'],
            (int) $this->arguments['pid']
        );
    }
}
