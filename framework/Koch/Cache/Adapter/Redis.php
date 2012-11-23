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
 * Cache Handler for Redis.
 *
 * Redis is an open source, advanced key-value store.
 * It is often referred to as a data structure server since
 * keys can contain strings, hashes, lists, sets and sorted sets.
 *
 * composer.json > require { "predis/service-provider": "dev-master" }
 * @link http://redis.io/
 * @link https://github.com/nicolasff/phpredis
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Redis extends AbstractCache implements CacheInterface
{
    /**
     * @var Redis
     */
    private $redis;

    public function __construct()
    {
        if (extension_loaded('redis') === false) {
            throw new Exception(
                'The PHP extension Redis (key-value store) is not loaded. You may enable it in "php.ini"!'
            );
        }

        $this->redis = new \Redis();

        // connect to redis instance
        if ($this->redis->connect('127.0.0.1') === false) {
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
     * @param  int $lifetime How long to cache the data, in minutes
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
