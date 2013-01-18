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
 * A pagination might use formelements, like additional dropdowns for Page and ItemsPerPage selection.
 */
class Pagination
{
    /**
     * The pagination adapter (data provider).
     * @var object \AdapterInterface
     */
    public $adapter;

    /* @var int The maximum number of items displayed per page. */
    public $maxResultsPerPage;

    /* @var int The current page. */
    public $currentPage;

    /* @var array Slice of the Dataset, containing all the results for the current page. */
    public $currentPageResults;

    /* @var int Total Number of Results */
    public $totalNumberOfResults;

    /* @var int Number of Pages (= getTotalNumberOfResults / maxItemsPerPage */
    public $numberOfPages;

    /**
     * Sets the pagination adapter (data provider).
     *
     * @param \Koch\Pagination\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns the pagination adapter (data provider).
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

    public function setMaxResultsPerPage($maxResultsPerPage)
    {
        if ($maxResultsPerPage < 1) {
            throw new \InvalidArgumentException('There must be more than 1 MaxResultsPerPage.');
        }

        $this->maxResultsPerPage = (int) $maxResultsPerPage;

        return $this;
    }

    public function getMaxResultsPerPage()
    {
        return $this->maxResultsPerPage;
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

    public function getTotalNumberOfResults()
    {
        if (null === $this->totalNumberOfResults) {
            $this->totalNumberOfResults = $this->getAdapter()->getTotalNumberOfResults();
        }

        return $this->totalNumberOfResults;
    }

    public function setTotalNumberOfResults($count)
    {
        $this->totalNumberOfResults = (int) $count;
    }

    /**
     * Returns the number of pages ( total results / results per page )
     * Example: 1000 results / 25 results per page = 40 pages.
     *
     * @return int Number of Pages.
     */
    public function getNumberOfPages()
    {
        if (null === $this->numberOfPages) {
            $this->numberOfPages = (int) ceil($this->getTotalNumberOfResults() / $this->getMaxResultsPerPage());
        }

        return $this->numberOfPages;
    }

    /**
     * Returns the last page. Same as getNumberOfPages(), but for convenience.
     */
    public function getLastPage()
    {
        return $this->getNumberOfPages();
    }

    /**
     * Returns true, if it's necessary to paginate and false, if not.
     *
     * @return bool True, if it is necessary to paginate. False otherwise.
     */
    public function haveToPaginate()
    {
        return $this->getTotalNumberOfResults() > $this->maxResultsPerPage;
    }

    public function getCurrentPageResults()
    {
        if (null === $this->currentPageResults) {
            $offset = ($this->getCurrentPage() - 1) * $this->getMaxResultsPerPage();
            $length = $this->getMaxResultsPerPage();
            $this->currentPageResults = $this->adapter->getSlice($offset, $length);
        }

        return $this->currentPageResults;
    }

    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage()
    {
        if ($this->hasPreviousPage() === false) {
            throw new \LogicException('There is not previous page.');
        }

        return $this->currentPage - 1;
    }

    public function hasNextPage()
    {
        return $this->currentPage < $this->getNumberOfPages();
    }

    public function getNextPage()
    {
        if ($this->hasNextPage() === false) {
            throw new \LogicException('There is not next page.');
        }

        return $this->currentPage + 1;
    }

    /**
     * Renders the pagination.
     *
     * @param string $renderer The pagination renderer to use.
     * @param array  $options  Additional options. Optional.
     */
    public function render($renderer = null, $options = null)
    {
        $renderer = new Renderer($renderer, $options, $this);

        return $renderer->render();
    }
}
