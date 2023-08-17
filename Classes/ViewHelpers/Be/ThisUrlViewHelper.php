<?php declare(strict_types=1);
namespace T3\FluidPageCache\ViewHelpers\Be;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the url of current page
 */
class ThisUrlViewHelper extends AbstractViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('showHost', 'boolean', 'If TRUE the hostname will be included');
        $this->registerArgument(
            'showRequestedUri',
            'boolean',
            'If TRUE the requested uri will be included',
            false,
            true
        );
        $this->registerArgument('urlencode', 'boolean', 'If TRUE the whole result will be URI encoded');
    }

    public function render(): string
    {
        $url = '';
        if ($this->arguments['showHost']) {
            $url .= ($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
        }
        if ($this->arguments['showRequestedUri']) {
            $url .= $_SERVER['REQUEST_URI'];
        }
        if ($this->arguments['urlencode']) {
            $url = urlencode($url);
        }
        return $url;
    }
}
