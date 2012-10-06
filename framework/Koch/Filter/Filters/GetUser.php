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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\User\User;

/**
 * Filter for Instantiation of the User Object.
 *
 * Purpose: Sets up the user session and user object.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Filters
 */
class GetUser implements FilterInterface
{
    private $user = null;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        unset($request, $response);

        // Create a user (Guest)
        $this->user->createUserSession();

        // Check for login cookie (Guest/Member)
        $this->user->checkLoginCookie();
        unset($this->user);
    }
}
