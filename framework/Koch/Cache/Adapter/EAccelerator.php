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
use Koch\Exception\Exception;

/**
 * Cache Handler for eAccelerator.
 *
 * eAccelerator was born in December 2004 as a fork of the Turck MMCache project (by Dmitry Stogov).
 * eAccelerator stores compiled PHP scripts in shared memory and executes code directly from it.
 * It creates locks only for a short time, while searching for a compiled PHP script in the cache,
 * so one script can be executed simultaneously by several engines. Files that can't fit in shared
 * memory are cached on disk only.
 *
 * @link http://eaccelerator.net/
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class EAccelerator extends AbstractCache implements CacheInterface
{
     /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (extension_loaded('eaccelerator') === false) {
            throw new Exception(
                'The PHP extension eAccelerator (cache) is not loaded! You may enable it in "php.ini!"'
            );
        }

        // @todo ensure eaccelerator 0.9.5 is in use
        // from 0.9.6 the user cache functions are removed
        /*if (false === function_exists('eaccelerator_info')) {
            die('eAccelerator isn\'t compiled with info support!');
        } else {
            $info = eaccelerator_info();
            $version = $info['name'];
        }*/

        parent::__construct($options);
    }

    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        if ( true === eaccelerator_get($key) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $data = eaccelerator_get($key);
        if ($data == false) {
            return false;
        }

        return unserialize($data);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key  Identifier for the data
     * @param  mixed   $data Data to be cached
     * @param  int $ttl  How long to cache the data, in minutes
     * @return boolean True if the data was successfully cached, false on failure
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
     * Delete data by key from cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean True if the data was successfully removed, false on failure
     */
    public function delete($key)
    {
        return eaccelerator_rm($key);
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
     *  Get stats and usage Informations for display from eAccelerator
     */
    public function stats()
    {
        // get info Get info about eAccelerator
        $eac_sysinfos['infos'] = eaccelerator_info();

        // List cached keys
        $keys = eaccelerator_list_keys();

        if (is_array($keys)) {
            foreach ($keys as $key) {
                $eac_sysinfo['keys'][] = $key;
            }
        }

        return null;
    }
}
