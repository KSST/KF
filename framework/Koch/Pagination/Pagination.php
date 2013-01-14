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

namespace Koch\Pagination;

/**
 * Pagination Class.
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
