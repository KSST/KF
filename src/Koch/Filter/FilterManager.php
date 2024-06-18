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

namespace Koch\Filter;

use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * FilterManager.
 *
 * Is a Intercepting-Filter (FilterChain).
 * The var $filters is an array containing the filters to be processed.
 * The method addFilter() adds filters to the array.
 * A filter has to implement the \Koch\Filter\FilterInterface,
 * it has to provide the executeFilter() method.
 */
class FilterManager
{
    private $filters = [];

    /**
     * addFilter
     * $filter is type-hinted, to ensure that the array filter only contains Filter-Objects.
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * processFilters executes each filter of the filters-array.
     *
     * @param request HttpRequestInterface
     * @param response HttpResponseInterface
     */
    public function processFilters(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        foreach ($this->filters as $filter) {
            $filter->executeFilter($request, $response);
        }
    }
}
