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

use Doctrine\ORM\Tools\Pagination\Paginator;
use Koch\Pagination\AdapterInterface;

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
     * @param \Doctrine\ORM\Query $query               A Doctrine Query or the QueryBuilder.
     * @param bool                $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        $this->paginator = new Paginator($query, $fetchJoinCollection);
    }

    /**
     * Returns the query.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return bool Whether the query joins a collection.
     */
    public function isQueryJoinsCollection()
    {
        return $this->paginator->getFetchJoinCollection();
    }

    public function getTotalNumberOfResults()
    {
        return count($this->paginator);
    }

    /**
     * @param int $offset
     * @param int $length
     */
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
