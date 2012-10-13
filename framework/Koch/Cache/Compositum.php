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
 * Compositum of Caches.
 *
 * This class represents a compositum for all registered cache adapters.
 * Another name for this class would be MultiCache.
 * A new cache object is added with addCache(), removed with removeCache().
 * The composition is fired via method cache().
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Compositum
{
    /**
     * Compositum stack.
     *
     * @var array
     */
    private $stack = array();

    /**
     * Adds an cache adapter to the compositum.
     *
     * @param object $adapter
     */
    public function addCache(CacheInterface $adapter)
    {
        $this->stack[] = $adapter;
    }

    /**
     * Removes an cache adapter from the compositum.
     *
     * @param string $adapter
     */
    public function removeCache($adapter)
    {
        if (array_key_exists($adapter, $this->stack)) {
            unset($this->stack[$adapter]);
        }
    }

    /**
     * Fires the compositum.
     */
    public function cache()
    {

    }
}
