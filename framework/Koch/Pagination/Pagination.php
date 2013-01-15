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

namespace Koch\Pagination;

/**
 * Koch Framework - Pagination Class.
 *
 * Pagination is the process of taking a result set and spreading it out
 * over several pages for making it easier to view.
 * This is an example of how a typical pagination bar looks like:
 * [Prev Page] [First Page] [1] [2] [3] [...] [Last Page] [Next Page]
 * You might find additional dropdowns for Page and ItemsPerPage selection.
 */
class Pagination
{
    /**
     * The pagination adapter.
     * @var object \AdapterInterface
     */
    public $adapter;

    /* @var int The maximum number of items displayed per page. */
    public $maxItemsPerPage;

    /* @var int The current page. */
    public $currentPage;

    /**
     * Sets the pagination adapter (which is the data provider).
     *
     * @param \Koch\Pagination\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns the pagination adapter.
     *
     * @return AdapterInterface The adapter.
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the pagination adapter (which is the data provider).
     *
     * @param object AdapterInterface
     * @return Pagination The pagination.
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function setMaxItemsPerPage($maxItemsPerPage)
    {
        if($maxItemsPerPage <= 1) {
            throw new \InvalidArgumentException('There must be 1 or more MaxItemsPerPage.');
        }

        $this->maxItemsPerPage = (int) $maxItemsPerPage;

        return $this;
    }

    public function getMaxItemsPerPage()
    {
        return $this->maxItemsPerPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage)
    {
        $currentPage = (int) $currentPage;

        $this->currentPage = ($currentPage < 1) ? 1 : $currentPage;

        return $this;
    }
}
