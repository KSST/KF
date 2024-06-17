<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Cache;

/**
 * Abstract base class for Cache Adapters.
 */
abstract class AbstractCache
{
    public $options = [
        'ttl'    => 900,
        'prefix' => 'kf_',
    ];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set options.
     *
     * @param array Options.
     */
    public function setOptions($options = [])
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * Sets a single option.
     *
     * @param string Key.
     * @param mixed Value.
     *
     * @return bool True, if successfull.
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new \InvalidArgumentException('TTL can not be lower than 1.');
                }
                $this->options['ttl'] = $value;
                break;
            case 'prefix':
                $prefix = (string) $value;
                if ($prefix === '') {
                    throw new \InvalidArgumentException('Prefix can not empty.');
                }
                $this->options['prefix'] = $prefix;
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('You tried to set the Option "%s", which is unknown.', $key)
                );
        }

        return true;
    }

    /**
     * Set Prefix for the cache key.
     *
     * @param string $prefix The prefix for all cache keys.
     *
     * @throws InvalidArgumentException if prefix is empty
     */
    public function setPrefix($prefix)
    {
        if (empty($prefix)) {
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
     * @param string $key Cache Key.
     *
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
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function __set($key, $value)
    {
        return (null === $value) ? $this->delete($key) : $this->store($key, $value);
    }

    /**
     * Get cache (magic).
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->fetch($key);
    }

    /**
     * Delete cache (magic).
     *
     * @param string $key
     *
     * @return bool
     */
    public function __unset($key)
    {
        return $this->delete($key);
    }

    /**
     * Checks if cache contains key (magic).
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->contains($key);
    }
}
