<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
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
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Wincache extends AbstractCache implements CacheInterface
{
    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        return wincache_ucache_exists($key);
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        return wincache_ucache_get($key);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key            Identifier for the data
     * @param  mixed   $data           Data to be cached
     * @param  int $cache_lifetime How long to cache the data, in minutes.
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $cache_lifetime = 0)
    {
        return wincache_ucache_set($key, $data, $cache_lifetime * 60);
    }

    /**
     * Delete data by key from cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean True if the data was successfully removed, false on failure
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }    
    
    public function clear()
    {
        wincache_ucache_clear();
    }

    /**
     * Get stats and usage Informations for display
     *
     * @todo if you want the feature, implement it ;)
     * Combine info array by taking a look at ucache_meminfo() and additional functions.
     * @link http://www.php.net/manual/en/function.wincache-ucache-meminfo.php
     */
    public function stats()
    {
    }
}
