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
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
interface CacheInterface
{
    // Checks cache for a stored variable
    public function contains($key);

    // Fetch a stored variable from the cache
    public function fetch($key);

    // Cache a variable in the data store
    public function store($key, $data, $cache_lifetime = 0);

    // Removes a stored variable from the cache
    public function delete($key);

    // Clears the cache
    public function clear();

    // Fetches cache adapter statistics
    public function stats();
}
