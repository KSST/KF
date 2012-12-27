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
 *
 */

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;
use Koch\Exception\Exception;

/**
 * Koch Framework - Cache Handler for Memcached.
 *
 * memcached is a high-performance, distributed memory object caching system, generic in nature,
 * but intended for use in speeding up dynamic web applications by alleviating database load.
 * memcached was developed by Danga Interactive to enhance the speed of LiveJournal.com.
 * You need two things to get this running: a memcache daemon (server) and the php extension memcached.
 *
 * More information can be obtained here:
 * @link http://www.danga.com/memcached/
 * @link http://libmemcached.org/libMemcached.html
 * @link http://php.net/manual/en/book.memcached.php
 *
 * See also the new implementation by Andrei Zmievsk based on libmemcached and memcached.
 * @link http://github.com/andreiz/php-memcached/tree/master
 * @link http://pecl.php.net/package/memcached
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Memcached extends AbstractCache implements CacheInterface
{
    /**
     * @var object PHP Memcached instance
     */
    protected $memcached = null;

    /**
     * Constructor.
     *
     * Instantiate and connect to Memcache Server
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (extension_loaded('memcached') === false) {
            throw new Exception(
                'The PHP extension memcache (cache) is not loaded! You may enable it in "php.ini"!'
            );
        }

        $this->memcached = new \Memcached;

        $this->options += array(
            'servers' => array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'persistent' => true
            )
        );

        parent::__construct($options);

        foreach ($this->options['servers'] as $server) {
            $this->memcached->addServer($server['host'], $server['port'], $server['persistent']);
        }

        $this->memcached->setOption(\Memcached::OPT_COMPRESSION, true);
        // LIBKETAMA compatibility will implicitly declare the following two things:
        #$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        #$this->memcached->setOption(Memcached::OPT_HASH, Memcached::MD5);
        $this->memcached->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
    }

    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        return (true === (bool) $this->memcached->get($key)) ? true : false;
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $result = $this->memcached->get($key);

        return ($result === false) ? false : $result;
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key  Identifier for the data
     * @param  mixed   $data Data to be cached
     * @param  integer $ttl  How long to cache the data (in minutes).
     * @return boolean True if the data was successfully cached, false on failure.
     */
    public function store($key, $data, $ttl = null)
    {
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }

        var_dump($this->memcached->set($key, $data, time() + $ttl));
        if ( $this->memcached->set($key, $data, time() + $ttl) === true ) {
            var_dump($this->memcached->getResultCode());
            return true;
        }

        return false;
    }

    /**
     * Deletes a $key or an array of $keys from the Memcache
     *
     * @param $key string
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Clears the Cache
     *
     * @return a flushed cache
     */
    public function clear()
    {
        return $this->memcached->flush();
    }

    /**
     * Display Memcached Usage Informations
     */
    public function stats()
    {
        $version    = $this->memcached->getVersion();
        $stats      = $this->memcached->getStats();
        $serverlist = $this->memcached->getServerList();

        // combine arrays
        return compact($version, $stats, $serverlist);
    }

    /**
     * Returns an the Memcached instance
     *
     * @return object \Memcached Cache Engine
     */
    public function getEngine()
    {
        return $this->memcached;
    }
}
