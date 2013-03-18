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

    /**
     * Checks cache for a stored variable.
     *
     * @param  string $key
     * @return bool   True, if key found, otherwise false.
     */
    public function contains($key);

    /**
     * Fetch a stored variable from the cache.
     *
     * @param  string $key
     * @return mixed
     */
    public function fetch($key);

    /**
     * Cache a variable in the data store.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $ttl
     * @return bool
     */
    public function store($key, $data, $ttl = 0);

    /**
     * Removes a stored variable from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key);

    /**
     * Clears the cache.
     *
     * @return boolean
     */
    public function clear();

    /**
     * Fetches cache adapter statistics.
     *
     * @return array
     */
    public function stats();
}
