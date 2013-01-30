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
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Koch Framework - Pagination Adapter for Doctrine Models.
 * This is a simple wrapper for Doctrine's Paginator.
 */
class Doctrine implements AdapterInterface
{
    /* @var \Doctrine\ORM\Tools\Pagination\Paginator */
    private $paginator;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query               A Doctrine Query or the QueryBuilder.
     * @param boolean                                        $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        $this->paginator = new Paginator($query, $fetchJoinCollection);
    }

    /**
     * Returns the query
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return Boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        return $this->paginator->getFetchJoinCollection();
    }

    public function getTotalNumberOfResults()
    {
        return count($this->paginator);
    }

    public function getSlice($offset, $length)
    {
        $this->paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);

        return $this->paginator->getIterator();
    }

    public function getArray()
    {
        return array();
    }
}
