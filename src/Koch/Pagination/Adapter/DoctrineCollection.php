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

namespace Koch\Pagination\Adapter;

use Doctrine\Common\Collections\Collection;
use Koch\Pagination\AdapterInterface;

/**
 * Pagination Adapter working with Doctrine Collections.
 */
class DoctrineCollection implements AdapterInterface
{
    /**
     * Constructor.
     *
     * @param Collection $collection A Doctrine collection.
     */
    public function __construct(private readonly Collection $collection)
    {
    }

    /**
     * Returns the collection.
     *
     * @return Collection The collection.
     */
    public function getCollection()
    {
        return $this->collection;
    }

    public function getArray()
    {
        return $this->collection->toArray();
    }

    public function getTotalNumberOfResults()
    {
        return $this->collection->count();
    }

    public function getSlice($offset, $length)
    {
        return $this->collection->slice($offset, $length);
    }
}
