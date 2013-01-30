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

namespace Koch\Mvc;

use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\Filter\FilterInterface;

/**
 * Interface for FrontController
 *
 * The Frontcontroller has to implement the following methods.
 */
interface FrontControllerInterface
{
    public function __construct(HttpRequestInterface $request, HttpResponseInterface $response);
    public function processRequest();
    public function addPreFilter(FilterInterface $filter);
    public function addPostFilter(FilterInterface $filter);
}
