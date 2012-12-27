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

namespace Koch\Cache;

/**
 * Koch Framework - Abstract Base Class for Cache Adapters.
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
    public function applyPrefix($key)
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
