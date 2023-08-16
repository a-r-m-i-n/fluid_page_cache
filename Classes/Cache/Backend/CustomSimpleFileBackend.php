<?php declare(strict_types = 1);
namespace T3\FluidPageCache\Cache\Backend;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2023 Armin Vieweg <info@v.ieweg.de>
 */
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;

class CustomSimpleFileBackend extends SimpleFileBackend
{
    public function __construct(string $context = '', array $options = [])
    {
        parent::__construct($context, $options);
    }

    public function all(): array
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->getCacheDirectory());
        if (!empty($this->cacheEntryFileExtension)) {
            $finder->name('*.' . $this->cacheEntryFileExtension);
        }

        $all = [];
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $all[] = $file->getFilename();
        }

        return $all;
    }
}
