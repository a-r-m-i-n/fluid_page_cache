<?php declare(strict_types = 1);
namespace T3\FluidPageCache\Cache\Backend;

/*  | This extension is made with ❤ for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2022 Armin Vieweg <info@v.ieweg.de>
 */
use TYPO3\CMS\Core\Cache\Backend\RedisBackend;

class CustomRedisBackend extends RedisBackend
{
    public function __construct($context, array $options = [])
    {
        parent::__construct($context, $options);
        $this->initializeObject();
    }

    public function all(): array
    {
        $keys = $this->redis->keys('identTags:*');
        $keysSanitized = [];
        foreach ($keys as $key) {
            $keysSanitized[] = substr($key, strlen('identTags:'));
        }
        return $keysSanitized;
    }
}
