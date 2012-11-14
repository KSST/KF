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
 * Cache Handler for Filecaching.
 *
 * The Filecache stores directly to disk.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class File extends AbstractCache implements CacheInterface
{
    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        $filepath = $this->filesystemKey($key);
        if (is_file($filepath)) {
            return true;
        }

        return false;
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $file = $this->filesystemKey($key);

        // try to open file, read-only
        if ((is_file($file)) and $r = fopen($file, 'r')) {
            // get the expiration time stamp
            $expires = (int) fread($r, 10);
            // if expiration time exceeds the current time, return the cache
            if (!$expires || $expires > time()) {
                $realsize = filesize($r) - 10;
                $cache = '';
                // read in a loop, because fread returns after 8192 bytes
                while ($chunk = fread($file, $realsize)) {
                    $cache .= $chunk;
                }
                fclose($r);

                return unserialize($cache);
            } else {
                // close and delete the expired cache
                fclose($r);
                $this->delete($key);
            }
        }

        return false;
    }

    /**
     * Stores data by key into cache
     *
     * @param string  $key            Identifier for the data
     * @param mixed   $data           Data to be cached
     * @param int $cache_lifetime How long to cache the data, in minutes
     *
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $cache_lifetime = 0)
    {
        // get name and lifetime
        $file = $this->filesystemKey($key);
        $cache_lifetime = str_pad((int) $cache_lifetime, 10, '0', STR_PAD_LEFT);

        // write key file
        $success = (bool) file_put_contents($file, $cache_lifetime * 60, LOCK_EX);

        // append serialized value to file
        if ($success) {
            return (bool) file_put_contents($file, serialize($data), FILE_APPEND | LOCK_EX);
        }

        return false;
    }

    /**
     * Delete data by key from cache
     *
     * @param string $key Identifier for the data
     *
     * @return boolean True if the data was successfully removed, false on failure
     */
    public function delete($key)
    {
        $filepath = $this->filesystemKey($key);
        if (is_file($filepath)) {
            return unlink($filepath);
        }

        return true;
    }

    /**
     * Clears the cache
     *
     * @return boolean True if cache cleared.
     */
    public function clear()
    {
        return false;
    }

    /**
     * Get stats and usage Informations for display
     */
    public function stats()
    {
        // @todo implement statistics for file cache usage
        return;
    }

    /**
     * Generates a filesystem cache key based on a given key.
     *
     * @param string $key The key to build the filesystem cache key from.
     *
     * @return string A filesystem cache key.
     */
    protected function filesystemKey($key)
    {
        return APPLICATION_CACHE_PATH . md5($key);
    }
}
