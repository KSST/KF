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

namespace Koch\Cache;

/**
 * Koch Framework - Cache
 */
class Cache
{
    private static $cacheAdapter;

    /**
     * Instantiates a cache adapter
     *
     * @param  string               $adapter The cache adapter to instantiate. Defaults to apc.
     * @return Koch_Cache_Interface Cache object of the requested adapter type.
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
     * @param  string                    $adapter Name of cache adapter, defaults to 'apc'.
     * @return \Koch\Cache\Adapter\Class
     */
    public static function factory($adapter = 'apc')
    {
        if ($adapter === 'eaccelerator') {
            $adapter = 'EAccelerator';
        }
        $class = '\Koch\Cache\Adapter\\' . ucfirst($adapter);
        $obj = new $class;

        return $obj;
    }

    /**
     * Checks, if data for a key is stored in the cache.
     *
     * @param  string $key
     * @return bool   True, the key/data exists.
     */
    public static function contains($key)
    {
        return self::$cacheAdapter->contains($key);
    }

    /**
     * Retrieves data by key from the cache.
     *
     * @param  type       $key
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
     * @param  type $key            The key to retrieve the data form the cache.
     * @param  type $data           The data to store in the cache.
     * @param  int  $cache_lifetime Cache lifetime in minutes.
     * @return bool
     */
    public static function store($key, $data, $cache_lifetime = 10)
    {
        return self::$cacheAdapter->store($key, $data, $cache_lifetime);
    }

    /**
     * Deletes data by key from the cache.
     *
     * @param  string $key
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
     * @return object
     */
    public static function fetchObject($key = null)
    {
        $object = self::$cacheAdapter->get($key);

        if (is_string($object)) {
            return unserialize($object);
        }

        return $object;
    }

    /**
     * Stores an object in the cache.
     *
     * @param  type    $key            The key for retrieving the object.
     * @param  type    $object         The object to store the cache.
     * @param  type    $cache_lifetime Cache liftime in minutes.
     * @return boolean True in caching success. False on caching failure.
     */
    public static function storeObject($key, $object, $cache_lifetime = 10)
    {
        return self::$cacheAdapter->set($key, serialize($object), $cache_lifetime);
    }
}
