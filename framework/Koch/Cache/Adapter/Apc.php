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
use Koch\Functions\Functions;

/**
 * Cache Handler for APC (Alternative PHP Cache).
 *
 * APC is a) an opcache and b) a memory based cache.
 *
 * Use this class only on PHP versions below PHP 5.5.0.
 *
 * As of PHP 5.5.0 using APC is no longer required, because
 * PHP ships an Opcode Cache (formerly Zend Optimizer+) by default.
 * For userland caching APCu is in the making (APC User Cache).
 *
 * @link http://de3.php.net/manual/de/ref.apc.php
 */
class Apc extends AbstractCache implements CacheInterface
{

     /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (extension_loaded('apc') === false) {
            throw new \Koch\Exception\Exception(
                'The PHP extension APC (Alternative PHP Cache) is not loaded. You may enable it in "php.ini"!'
            );
        }

        $enabled = ini_get('apc.enabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('apc.enable_cli');
        }

        if ($enabled === false) {
            throw new \Koch\Exception\Exception(
                "The PHP extension APC (Alternative PHP Cache) is not loaded." .
                "You may enable it with 'apc.enabled=1' and 'apc.enable_cli=1'!"
            );
        }

        parent::__construct($options);
    }

    /**
     * Read a key from the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean True if the data was successfully fetched from the cache, false on failure
     */
    public function fetch($key)
    {
        return apc_fetch($key);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key       Identifier for the data
     * @param  string  $data      Data to be cached
     * @param  int $ttl       How long to cache the data, in minutes.
     * @param  boolean $overwrite If overwrite true, key will be overwritten.
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $ttl = null, $overwrite = false)
    {
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }

        if ($key === null) {
            return false;
        } elseif ($overwrite == false) {
            return apc_add($key, $data, $ttl);
        } else { // overwrite

            return apc_store($key, $data, $ttl);
        }
    }

    /**
     * Removes a stored variable from the
     *
     * @link http://php.net/manual/en/function.apc-delete.php
     * @param  string $keys Identifier for the data
     * @return int    Number of keys deleted.
     */
    public function delete($keys)
    {
        $keys = (array) $keys;
        $keys_deleted = 0;

        foreach ($keys as $key) {
            if (true === apc_delete($key)) {
                $keys_deleted++;
            }
        }

        return $keys_deleted;
    }

    /**
     * Clears the APC cache
     *
     * @link http://php.net/manual/en/function.apc-clear-cache.php
     * @param  string $cache_type [optional] all, user, opcode <p>
     *                            If cache_type is "user", the user cache will be cleared;
     *                            otherwise, the system cache (cached files) will be cleared. </p>
     * @return bool   Returns true on success or false on failure.
     */
    public function clear($cache_type = 'user')
    {
        if (extension_loaded('apcu') === true) {
            return apc_clear_cache();
        }

        if ($cache_type === 'all') {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');
        }

        return apc_clear_cache($cache_type);
    }

    /**
     * Checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        return apc_exists($key);
    }

    /**
     * Get stats and usage Informations for display from APC
     * 1. Shared Memory Allocation
     * 2. Cache Infos / Meta-Data
     */
    public function stats()
    {
        $info = array();
        // Retrieve APC Version
        $info['version'] = phpversion('apc');
        $info['phpversion'] = phpversion();

        /**
         * ========================================================
         *   Retrieves APC's Shared Memory Allocation information
         * ========================================================
         */
        $info['sma_info'] = array();

        if (true === function_exists('apc_sma_info')) {
            $info['sma_info'] = apc_sma_info(true);

            // Calculate "APC Memory Size" (Number of Segments * Size of Segment)
            $memsize = $info['sma_info']['num_seg'] * $info['sma_info']['seg_size'];
            $info['sma_info']['mem_size'] = $memsize;

            // Calculate "APC Memory Usage" ( mem_size - avail_mem )
            $memusage = $info['sma_info']['mem_size'] - $info['sma_info']['avail_mem'];
            $info['sma_info']['mem_used'] = $memusage;

            // Calculate "APC Free Memory Percentage" ( mem_size*100/mem_used )
            $memsize_total = $info['sma_info']['avail_mem'] * 100;
            $avail_mem_percent = sprintf('(%.1f%%)', $memsize_total  / $info['sma_info']['mem_size']);
            $info['sma_info']['mem_avail_percentage'] = $avail_mem_percent;
        }

        if (true === function_exists('apc_cache_info')) {

            // Retrieves cached information and meta-data from APC's data store
            $info['cache_info'] = apc_cache_info();

            #\Koch\Debug\Debug::printR(apc_cache_info());
            $info['cache_info']['cached_files'] = count($info['cache_info']['cache_list']);
            $info['cache_info']['deleted_files'] = count($info['cache_info']['deleted_list']);

            /**
             * ========================================================
             *   System Cache Informations
             * ========================================================
             */
            $info['system_cache_info'] = apc_cache_info('system', false); // set "false" for details
            // Calculate "APC Hit Rate Percentage"
            $hits = ($info['system_cache_info']['num_hits'] + $info['system_cache_info']['num_misses']);

            // div by zero fix
            if ($hits == 0) {
                $hits = 1;
            }

            $hit_rate_percentage = $info['system_cache_info']['num_hits'] * 100 / $hits;
            $info['system_cache_info']['hit_rate_percentage'] = sprintf('(%.1f%%)', $hit_rate_percentage);

            // Calculate "APC Miss Rate Percentage"
            $miss_percentage = $info['system_cache_info']['num_misses'] * 100 / $hits;
            $info['system_cache_info']['miss_rate_percentage'] = sprintf('(%.1f%%)', $miss_percentage);
            $info['system_cache_info']['files_cached'] = count($info['system_cache_info']['cache_list']);
            $info['system_cache_info']['files_deleted'] = count($info['system_cache_info']['deleted_list']);

            // Request Rate (hits, misses) / cache requests/second
            $start_time = (time() - $info['system_cache_info']['start_time']);

            // div by zero fix
            if ($start_time == 0) {
                $start_time = 1;
            }

            $rate = (($info['system_cache_info']['num_hits'] + $info['system_cache_info']['num_misses']) / $start_time);
            $info['system_cache_info']['req_rate'] = sprintf('%.2f', $rate);

            $hit_rate = ($info['system_cache_info']['num_hits']) / $start_time;
            $info['system_cache_info']['hit_rate'] = sprintf('%.2f', $hit_rate);

            $miss_rate = ($info['system_cache_info']['num_misses'] / $start_time);
            $info['system_cache_info']['miss_rate'] = sprintf('%.2f', $miss_rate);

            $insert_rate = (($info['system_cache_info']['num_inserts']) / $start_time);
            $info['system_cache_info']['insert_rate'] = sprintf('%.2f', $insert_rate);

            // size
            if (isset($info['system_cache_info']['mem_size']) and $info['system_cache_info']['mem_size'] > 0) {
                $info['system_cache_info']['size_files'] = Functions::getSize($info['system_cache_info']['mem_size']);
            } else {
                $info['system_cache_info']['size_files'] = 0;
            }
        }

        $info['settings'] = ini_get_all('apc');

        /**
         * ini_get_all array mod: for each accessvalue
         * add the name of the PHP ACCESS CONSTANTS as 'accessname'
         * @todo: cleanup?
         */
        foreach ($info['settings'] as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 == 'access') {
                    $name = '';

                    // accessvalue => constantname
                    if ($value2 == '1') {
                        $name = 'PHP_INI_USER';
                    }
                    if ($value2 == '2') {
                        $name = 'PHP_INI_PERDIR';
                    }
                    if ($value2 == '4') {
                        $name = 'PHP_INI_SYSTEM';
                    }
                    if ($value2 == '7') {
                        $name = 'PHP_INI_ALL';
                    }

                    // add accessname to the original array
                    $info['settings'][$key]['accessname'] = $name;
                    unset($name);
                }
            }
        }

        #$info['sma_info']['size_vars']  = Functions::getsize($cache_user['mem_size']);

        return $info;
    }
}
