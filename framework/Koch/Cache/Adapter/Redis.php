<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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
 * Koch Framework - Cache Handler for Redis.
 *
 * Redis is an open source, advanced key-value store.
 * It is often referred to as a data structure server since
 * keys can contain strings, hashes, lists, sets and sorted sets.
 *
 * composer.json > require { "predis/service-provider": "dev-master" }
 * @link http://redis.io/
 * @link https://github.com/nicolasff/phpredis
 */
class Redis extends AbstractCache implements CacheInterface
{
     /**
     * @var object PHP Redis instance
     */
    private $redis;

     /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (extension_loaded('redis') === false) {
            throw new Exception(
                'The PHP extension Redis (key-value store) is not loaded. You may enable it in "php.ini"!'
            );
        }

        $this->redis = new \Redis();

        // connect to redis instance
        if ($this->redis->connect('127.0.0.1', 6379) === false) {
            throw new RuntimeException('Connection to Redis database failed. Check configuration.');
        }

        // configure
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
        $this->redis->setOption(Redis::OPT_PREFIX, 'KochFramework:'); // use custom prefix on all keys
    }

    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  string|boolean If key not found, returns FALSE. Otherwise, returns the value of the key.
     */
    public function fetch($key)
    {
        return $this->redis->get($key);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key      Identifier for the data
     * @param  mixed   $data     Data to be cached
     * @param  integer $lifetime How long to cache the data, in minutes
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $lifetime = 0)
    {
        if ($lifetime === 0) {
            return false;
        }

        return $this->redis->set($key, $data, $lifetime);
    }

    /**
     * Remove specified keys.
     *
     * @param  array|string $key The key(s) to delete.
     * @return boolean      True, if one or more keys deleted. False otherwise.
     */
    public function delete($key)
    {
        return (count($this->redis->delete($key)) >= 1) ? true : false;
    }

    /**
     * Removes all entries from the current database.
     *
     * @return boolean Always True.
     */
    public function clear()
    {
        return $this->redis->flushDB();
    }

    public function stats()
    {
        $info = $this->redis->info();

        return array(
            Cache::STATS_HITS   => false,
            Cache::STATS_MISSES => false,
            Cache::STATS_UPTIME => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE       => $info['used_memory'],
            Cache::STATS_MEMORY_AVAILIABLE  => false
        );
    }
}
