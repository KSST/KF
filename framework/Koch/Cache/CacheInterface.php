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
 * Interface for all Cache Adapters to implement.
 */
interface CacheInterface
{
    // keys of the statistic array, returned by stats()
    const STATS_HITS = 'hits';
    const STATS_MISSES = 'misses';
    const STATS_UPTIME = 'uptime';
    const STATS_MEMORY_USAGE = 'memory_usage';
    const STATS_MEMORY_AVAILABLE = 'memory_available';

    // Checks cache for a stored variable
    public function contains($key);

    // Fetch a stored variable from the cache
    public function fetch($key);

    // Cache a variable in the data store
    public function store($key, $data, $ttl = 0);

    // Removes a stored variable from the cache
    public function delete($key);

    // Clears the cache
    public function clear();

    // Fetches cache adapter statistics
    public function stats();
}
