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
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Pagination Adapter for Doctrine Models.
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
        return $this->paginator->getQuery()->getResult();
    }
}
