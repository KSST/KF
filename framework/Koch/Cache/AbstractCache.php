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
    protected $options = array(
        'ttl' => 900,
        'prefix' => 'kf_'
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    /**
     * Set Prefix for the cache key.
     *
     * @param  string                   $prefix The prefix for all cache keys.
     * @throws InvalidArgumentException if prefix is empty
     */
    public function setPrefix($prefix)
    {
        if (empty($prefix) === true) {
            throw new \InvalidArgumentException('Prefix must not be empty.');
        }

        $this->options['prefix'] = $prefix;
    }

    /**
     * Get Prefix for the cache key.
     *
     * @return string The cache prefix
     */
    public function getPrefix()
    {
        return $this->options['prefix'];
    }

    /**
     * Prepends key with prefix.
     *
     * @param  string $key Cache Key.
     * @return string Prefixed Cache Key.
     */
    public function prefixKey($key)
    {
        return $this->options['prefix'] . $key;
    }

    /**
     * Set cache (magic)
     * If value is null, the key is deleted.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return boolean
     */
    public function __set($key, $value)
    {
        return null === $value ? $this->delete($key) : $this->store($key, $value);
    }

    /**
     * Get cache (magic)
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->fetch($key);
    }

    /**
     * Delete cache (magic)
     *
     * @param  string  $key
     * @return boolean
     */
    public function __unset($key)
    {
        return $this->delete($key);
    }
}
