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
 * Koch Framework - Cache Handler for Xcache.
 *
 * XCache is a open-source opcode cacher, which means that it accelerates the performance of PHP on servers.
 * It optimizes performance by removing the compilation time of PHP scripts by caching the compiled state of
 * PHP scripts into the shm (RAM) and uses the compiled version straight from the RAM. This will increase
 * the rate of page generation time by up to 5 times as it also optimizes many other aspects of php scripts
 * and reduce serverload.
 * The XCache project is lead by mOo who is also a developer of the Lighttpd, the Webserver also known as lighty.
 *
 * @link http://xcache.lighttpd.net/
 * @link http://xcache.lighttpd.net/wiki/XcacheApi
 */
class Xcache extends AbstractCache implements CacheInterface
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('xcache')) {
            throw new Exception('The PHP extension "xcache" is not loaded. You may enable it in "php.ini"!');
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
        return xcache_isset($key);
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
        return $this->contains($key) ? unserialize(xcache_get($key)) : false;
    }

    /**
     * Stores data by key into cache.
     *
     * @param string $key  Identifier for the data
     * @param mixed  $data Data to be cached
     * @param int    $ttl  How long to cache the data, in minutes
     *
     * @return bool True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $ttl = 0)
    {
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }

        return xcache_set($key, serialize($data), $ttl);
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
        $this->checkAuthorizationIsOff();

        return xcache_clear_cache(XC_TYPE_VAR, 0);
    }

    /**
     * Returns an array with stats and usage informations for display.
     *
     * @return array
     */
    public function stats()
    {
        $this->checkAuthorizationIsOff();

        $info = xcache_info(XC_TYPE_VAR, 0);

        return [
            CacheInterface::STATS_HITS             => $info['hits'],
            CacheInterface::STATS_MISSES           => $info['misses'],
            CacheInterface::STATS_UPTIME           => null,
            CacheInterface::STATS_MEMORY_USAGE     => $info['size'],
            CacheInterface::STATS_MEMORY_AVAILABLE => $info['avail'],
        ];
    }

    /**
     * Checks that xcache.admin.enable_auth is Off.
     *
     * @throws \BadMethodCallException When xcache.admin.enable_auth is On
     */
    protected function checkAuthorizationIsOff()
    {
        if (ini_get('xcache.admin.enable_auth')) {
            throw new \BadMethodCallException(
                _('Feature disabled. Please set "xcache.admin.enable_auth" to "Off" in your php.ini.')
            );
        }
    }
}
