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

namespace Koch\Pagination\Adapter;

use Koch\Pagination\AdapterInterface;

/**
 * Koch Framework - Pagination Adapter for NativeArrays.
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
     * @param integer $offset The offset.
     * @param integer $length The length.
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return array_slice($this->array, $offset, $length);
    }
}
