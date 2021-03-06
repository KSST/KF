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
use Koch\Exception\Exception;

/**
 * Koch Framework - Cache Handler for eAccelerator.
 *
 * eAccelerator was born in December 2004 as a fork of the Turck MMCache project (by Dmitry Stogov).
 * eAccelerator stores compiled PHP scripts in shared memory and executes code directly from it.
 * It creates locks only for a short time, while searching for a compiled PHP script in the cache,
 * so one script can be executed simultaneously by several engines. Files that can't fit in shared
 * memory are cached on disk only.
 *
 * @link http://eaccelerator.net/
 */
class EAccelerator extends AbstractCache implements CacheInterface
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (extension_loaded('eaccelerator') === false) {
            throw new Exception(
                'The PHP extension eAccelerator (cache) is not loaded! You may enable it in "php.ini!"'
            );
        }

        // @todo ensure eaccelerator 0.9.5 is in use
        // from 0.9.6 the user cache functions are removed
        /*if (false === function_exists('eaccelerator_info')) {
            throw new \Exception('eAccelerator isn\'t compiled with info support!');
        } else {
            $info = eaccelerator_info();
            $version = $info['name'];
        }*/

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
        if (true === eaccelerator_get($key)) {
            return true;
        } else {
            return false;
        }
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
        $data = eaccelerator_get($key);
        if ($data === false) {
            return false;
        }

        return unserialize($data);
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
    public function store($key, $data, $ttl = null)
    {
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }

        $data = serialize($data);

        return eaccelerator_put($key, $data, $ttl);
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
        return eaccelerator_rm($key);
    }

    /**
     * Clears the cache.
     *
     * @return bool True if cache cleared.
     */
    public function clear()
    {
        return false;
    }

    /**
     *  Get stats and usage Informations for display from eAccelerator.
     */
    public function stats()
    {
        $infos = [];

        $infos['infos'] = eaccelerator_info();

        $keys = eaccelerator_list_keys();

        if (is_array($keys)) {
            foreach ($keys as $key) {
                $infos['keys'][] = $key;
            }
        }

        return $infos;
    }
}
