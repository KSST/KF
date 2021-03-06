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

namespace Koch\Cache;

/**
 * Koch Framework - Cache.
 */
class Cache
{
    private static $cacheAdapter;

    /**
     * Instantiates a cache adapter.
     *
     * @param string $adapter The cache adapter to instantiate. Defaults to apc.
     *
     * @return KochCache_Interface Cache object of the requested adapter type.
     */
    public static function instantiate($adapter = 'apc')
    {
        if (self::$cacheAdapter === null) {
            self::$cacheAdapter = self::factory($adapter);
        }

        return self::$cacheAdapter;
    }

    /**
     * Factory method for instantiation of cache adapters.
     *
     * @param string $adapter Name of cache adapter, defaults to 'apc'.
     * @param array  $options
     *
     * @return \Koch\Cache\Adapter\Class
     */
    public static function factory($adapter = 'apc', $options = [])
    {
        if ($adapter === 'eaccelerator') {
            $adapter = 'EAccelerator';
        }
        $class = '\Koch\Cache\Adapter\\' . ucfirst($adapter);
        $obj   = new $class($options);

        return $obj;
    }

    /**
     * Checks, if data for a key is stored in the cache.
     *
     * @param string $key
     *
     * @return bool True, the key/data exists.
     */
    public static function contains($key)
    {
        return self::$cacheAdapter->contains($key);
    }

    /**
     * Retrieves data by key from the cache.
     *
     * @param type $key
     *
     * @return mixed|null Returns data or null.
     */
    public static function fetch($key = null)
    {
        $data = self::$cacheAdapter->fetch($key);

        return ($data) ? $data : null;
    }

    /**
     * Stores data by key to the cache.
     *
     * @param string $key  The key to retrieve the data form the cache.
     * @param type   $data The data to store in the cache.
     * @param int    $ttl  Cache lifetime in minutes.
     *
     * @return bool
     */
    public static function store($key, $data, $ttl = 10)
    {
        return self::$cacheAdapter->store($key, $data, $ttl);
    }

    /**
     * Deletes data by key from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function delete($key)
    {
        return self::$cacheAdapter->delete($key);
    }

    /**
     * Clears the cache completely.
     *
     * @return bool
     */
    public static function clear()
    {
        return self::$cacheAdapter->clear();
    }

    /**
     * Retrieves an object from the cache.
     *
     * @param string $key
     *
     * @return object
     */
    public static function fetchObject($key = null)
    {
        $object = self::$cacheAdapter->fetch($key);

        if (is_string($object)) {
            return unserialize($object);
        }
    }

    /**
     * Stores an object in the cache.
     *
     * @param string    $key    The key for retrieving the object.
     * @param \stdClass $object The object to store the cache.
     * @param int       $ttl    Cache liftime in minutes.
     *
     * @return bool True in caching success. False on caching failure.
     */
    public static function storeObject($key, $object, $ttl = 10)
    {
        return self::$cacheAdapter->store($key, serialize($object), $ttl);
    }
}
