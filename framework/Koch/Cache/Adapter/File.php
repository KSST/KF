<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;

/**
 * Koch Framework - Cache Handler for Filecaching.
 *
 * The Filecache stores directly to disk.
 */
class File extends AbstractCache implements CacheInterface
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
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
        $lifetime = -1;
        $file     = realpath($this->createFilenameFromKey($key));

        if (is_file($file) === false) {
            return false;
        }

        $resource = fopen($file, 'r');

        if (false !== ($line = fgets($resource))) {
            $lifetime = (integer) $line;
        }

        fclose($resource);

        return ($lifetime > time()) ? true : false;
    }

    /**
     * Read a key from the cache.
     *
     * @param string $key Identifier for the data
     *
     * @return mixed boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $file = $this->createFilenameFromKey($key);

        if (is_file($file) === false) {
            return false;
        }

        $resource = fopen($file, 'r');

        if (false !== ($line = fgets($resource))) {
            $lifetime = (integer) $line;
        }

        // if liftime is lower then current time, the cache has become invalid
        if ($lifetime < time()) {
            fclose($resource);

            return false;
        }

        $data = '';
        while (false !== ($line = fgets($resource))) {
            $data .= $line;
        }

        fclose($resource);

        return unserialize($data);
    }

    /**
     * Stores data by key into cache.
     *
     * @param string $key      Identifier for the data
     * @param string $data     Data to be cached
     * @param int    $lifetime How long to cache the data, in minutes
     *
     * @return bool True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $lifetime = 3600)
    {
        // do not write a cache file, if lifetime is 0
        if ($lifetime === 0) {
            return false;
        }

        $lifetime = time() + $lifetime;

        $file = $this->createFilenameFromKey($key);

        $data = serialize($data);

        $content = $lifetime . PHP_EOL . $data;

        // create folder, if not existing
        $filepath = pathinfo($file, PATHINFO_DIRNAME);
        if (is_dir($filepath) === false) {
            mkdir($filepath, 0777, true);
        }

        return (bool) file_put_contents($file, $content);
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
        $file = $this->createFilenameFromKey($key);

        return (bool) @unlink($file);
    }

    /**
     * Clears the cache.
     *
     * @return bool True if cache cleared.
     */
    public function clear()
    {
        array_map('unlink', glob(APPLICATION_CACHE_PATH . '*.kf.cache'));

        return true;
    }

    /**
     * Get stats and usage Informations for display.
     */
    public function stats()
    {
        // @todo implement statistics for file cache usage
        // usage 'filecache_' prefix to identify files
        // return number of files and total size of files, disk space left
        return [
            CacheInterface::STATS_HITS             => null,
            CacheInterface::STATS_MISSES           => null,
            CacheInterface::STATS_UPTIME           => null,
            CacheInterface::STATS_MEMORY_USAGE     => null,
            CacheInterface::STATS_MEMORY_AVAILABLE => null,
        ];
    }

    /**
     * Generates a filesystem cache key based on a given key.
     *
     * @param string $key The key to build the filesystem cache key from.
     *
     * @return string A filesystem cache key.
     */
    public function createFilenameFromKey($key)
    {
        $id = md5($key);

        return APPLICATION_CACHE_PATH . $id . '.kf.cache';
    }
}
