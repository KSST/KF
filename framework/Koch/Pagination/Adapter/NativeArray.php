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

namespace Koch\Pagination\Adapter;

use Koch\Pagination\AdapterInterface;

/**
 * Pagination Adapter for NativeArrays.
 */
class NativeArray implements AdapterInterface
{
    private $array;

    /**
     * Constructor.
     *
     * @param array $array The array.
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * Returns the array.
     *
     * @return array The array.
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Returns the total number of results.
     *
     * @return integer The total number of results.
     */
    public function getTotalNumberOfResults()
    {
        return count($this->array);
    }

    /**
     * Returns a slice of the result set.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return array_slice($this->array, $offset, $length);
    }
}
