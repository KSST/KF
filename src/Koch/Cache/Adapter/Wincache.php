<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;

/**
 * Cache Handler for Windows Cache.
 *
 * Windows Cache Extension for PHP is a PHP accelerator that is used to
 * increase the speed of PHP applications running on Windows and Windows Server.
 * Microsoft Internet Information Services (MS IIS) is required.
 *
 * Detailed description can be found on the http://www.iis.net/download/WinCacheForPhp page.
 * Latest source and bug information you can find here http://pecl.php.net/package/wincache.
 * Documentation is here http://www.php.net/manual/en/book.wincache.php.
 *
 * Download: http://sourceforge.net/projects/wincache/files/
 * It's a PECL extension. http://pecl.php.net/package/WinCache
 */
class Wincache extends AbstractCache implements CacheInterface
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (extension_loaded('wincache') === false) {
            throw new Exception(
                'The PHP extension wincache is not loaded! You may enable it in "php.ini"!'
            );
        }

        parent::__construct($options);
    }

    /**
     * Contains checks if a key exists in the cache.
     *
     * @param string $key Identifier for the data
     *
     * @return bool true|false
     */
    public function contains($key)
    {
        return wincache_ucache_exists($key);
    }

    /**
     * Read a key from the cache.
     *
     * @param string $key Identifier for the data
     *
     * @return mixed bool FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        return wincache_ucache_get($key);
    }

    /**
     * Stores data by key into cache.
     *
     * @param string $key  Identifier for the data
     * @param mixed  $data Data to be cached
     *
     * @return bool True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $lifetime = 0)
    {
        return wincache_ucache_set($key, $data, (int) $lifetime * 60);
    }

    /**
     * Delete data by key from cache.
     *
     * @param string $key Identifier for the data
     *
     * @return bool True if the data was successfully removed, false on failure
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }

    /**
     * Clears the cache.
     *
     * @return bool
     */
    public function clear()
    {
        return wincache_ucache_clear();
    }

    /**
     * Returns an array with stats and usage informations for display.
     *
     * @return array
     */
    public function stats()
    {
        $info    = wincache_ucache_info();
        $meminfo = wincache_ucache_meminfo();

        return [
            CacheInterface::STATS_HITS              => $info['total_hit_count'],
            CacheInterface::STATS_MISSES            => $info['total_miss_count'],
            CacheInterface::STATS_UPTIME            => $info['total_cache_uptime'],
            CacheInterface::STATS_MEMORY_USAGE      => $meminfo['memory_total'],
            CacheInterface::STATS_MEMORY_AVAILIABLE => $meminfo['memory_free'],
        ];
    }
}
