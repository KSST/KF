<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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
     * addFilter method
     * $filter is type-hinted, to ensure that the array filter only contains Filter-Objects.
     *
     * @param FilterInterface $filter
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
