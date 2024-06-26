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
 * Interface for all Cache Adapters to implement.
 */
interface CacheInterface
{
    // keys of the statistic array, returned by stats()
    public const STATS_HITS             = 'hits';
    public const STATS_MISSES           = 'misses';
    public const STATS_UPTIME           = 'uptime';
    public const STATS_MEMORY_USAGE     = 'memory_usage';
    public const STATS_MEMORY_AVAILABLE = 'memory_available';

    /**
     * Checks cache for a stored variable.
     *
     * @param string $key
     *
     * @return bool True, if key found, otherwise false.
     */
    public function contains($key);

    /**
     * Fetch a stored variable from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key);

    /**
     * Cache a variable in the data store.
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return bool
     */
    public function store($key, $data, $ttl = 0);

    /**
     * Removes a stored variable from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * Clears the cache.
     *
     * @return bool
     */
    public function clear();

    /**
     * Fetches cache adapter statistics.
     *
     * @return array
     */
    public function stats();
}
