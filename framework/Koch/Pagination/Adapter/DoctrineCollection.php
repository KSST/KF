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
use Doctrine\Common\Collections\Collection;

/**
 * Pagination Adapter working with Doctrine Collections.
 */
class DoctrineCollection implements AdapterInterface
{
    /* @var Doctrine\Common\Collections\Collection */
    private $collection;

    /**
     * Constructor.
     *
     * @param Collection $collection A Doctrine collection.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
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
