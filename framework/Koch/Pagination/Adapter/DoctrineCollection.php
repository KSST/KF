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
use Doctrine\Common\Collections\Collection;

/**
 * Doctrine Collection Adapter.
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
