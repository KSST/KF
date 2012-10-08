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
 * Cache Handler for APC (Alternative PHP Cache).
 *
 * APC is a) an opcache and b) a memory based cache.
 *
 * @link http://de3.php.net/manual/de/ref.apc.php
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Apc extends AbstractCache implements CacheInterface
{

    public function __construct()
    {
        if (extension_loaded('apc') === false) {
            throw new Exception(
                'The PHP extension APC (Alternative PHP Cache) is not loaded. You may enable it in "php.ini"!'
            );
        }
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
     * @param  string  $key            Identifier for the data
     * @param  mixed   $data           Data to be cached
     * @param  int $cache_lifetime How long to cache the data, in minutes.
     * @param  boolean $overwrite      If overwrite true, key will be overwritten.
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $cache_lifetime = 0, $overwrite = false)
    {
        if ($key === null) {
            return false;
        } elseif ($overwrite == false) {
            return apc_add($key, $data, $cache_lifetime * 60);
        } else { // overwrite

            return apc_store($key, $data, $cache_lifetime * 60);
        }
    }

    /**
     * Removes a stored variable from the
     *
     * @link http://php.net/manual/en/function.apc-delete.php
     * @param  string|array $key Identifier for the data
     * @return int          Number of keys deleted.
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
     * @param string $cache_type [optional] <p>
     * If cache_type is "user", the user cache will be cleared;
     * otherwise, the system cache (cached files) will be cleared. </p>
     * @return bool Returns true on success or false on failure.
     */
    public function clear($cache_type = null)
    {
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
        if (false === function_exists('apc_sma_info')) {
            $info['sma_info'] = apc_sma_info(); // set "false" for details

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

            $req_rate = (($info['system_cache_info']['num_hits'] + $info['system_cache_info']['num_misses']) / $start_time);
            $info['system_cache_info']['req_rate'] = sprintf('%.2f', $req_rate);

            $hit_rate = ($info['system_cache_info']['num_hits']) / $start_time;
            $info['system_cache_info']['hit_rate'] = sprintf('%.2f', $hit_rate);

            $miss_rate = ($info['system_cache_info']['num_misses'] / $start_time);
            $info['system_cache_info']['miss_rate'] = sprintf('%.2f', $miss_rate);

            $insert_rate = (($info['system_cache_info']['num_inserts']) / $start_time);
            $info['system_cache_info']['insert_rate'] = sprintf('%.2f', $insert_rate);

            // size
            $info['system_cache_info']['size_files'] = \Koch\Functions\Functions::getsize($info['system_cache_info']['mem_size']);
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

        #$info['sma_info']['size_vars']  = \Koch\Functions\Functions::getsize($cache_user['mem_size']);

        return $info;
    }

    /**
     * Stores a file in the bytecode cache, bypassing all filters
     *
     * @link http://www.php.net/manual/en/function.apc-compile-file.php
     * @param  string $filename
     * @return bool   success
     */
    public function compileFile($filename)
    {
        return apc_compile_file($filename);
    }

    /**
     * Stores a directory in the bytecode cache, bypassing all filters
     *
     * @param  string $root
     * @param  bool   $recursively
     * @return bool   success
     */
    public function compileDir($root, $recursively = true)
    {
        $compiled = true;

        switch ($recursively) {
            // compile files in subdirectories
            // WATCH OUT ! RECURSION
            case true:
                foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $dir) {
                    $compiled = $compiled && $this->compile_dir($dir, $recursively);
                }

            // compile files in root directory
            case false:
                foreach (glob($root . DIRECTORY_SEPARATOR . '*.php') as $filename) {
                    $compiled = $compiled && $this->compile_file($filename);
                }
                break;
        }

        return $compiled;
    }
}
