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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Filter for Permissions / RBACL Checks.
 *
 * Purpose: Perform an Permissions / RBACL Check
 */
class Permissions implements FilterInterface
{
    private $user  = null;
    private $rbacl = null;

    public function __construct(Koch\User\User $user)
    {
        $this->user = $user;

        $this->rbacl = new \Koch\Permissions\RBACL();
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        if (false === $this->rbacl->isAuthorized($actionname, $this->user->getUserId())) {
            // @todo errorpage, no permission to perform this action. access denied.
            $response->redirect();
        }
    }
}
