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

namespace Koch\Filter;

use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Interface \Koch\Filter\FilterInterface
 */
interface FilterInterface
{
    /**
     * @return void
     */
    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response);
}
