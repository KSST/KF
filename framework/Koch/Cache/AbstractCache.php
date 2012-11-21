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
 * Abstract Base Class for Cache Adapters.
 */
abstract class AbstractCache
{
    /**
     * Prefix for the cache key.
     *
     * @var mixed Defaults to 'kf_'.
     */
    //protected $prefix = 'kf_';

    /**
     * Set Prefix for the cache key.
     *
     * @param  string                   $prefix The prefix for all cache keys.
     * @throws InvalidArgumentException if prefix is empty
     */
    /*public function setPrefix($prefix)
    {
        if (empty($prefix) === true) {
            throw new \InvalidArgumentException('Prefix must not be empty.');
        }

        $this->prefix = $prefix;
    }*/

    /**
     * Get Prefix for the cache key.
     *
     * @return string The cache prefix
     */
    /*public function getPrefix()
    {
        return $this->prefix;
    }*/

    /**
     * Prepends key with prefix.
     *
     * @param string $key Cache Key.
     * @return string Prefixed Cache Key.
     */
    /*public function applyPrefix($key)
    {
        return $this->prefix . $key;
    }*/
}
