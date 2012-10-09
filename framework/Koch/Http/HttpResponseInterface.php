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

namespace Koch\Http;

/**
 * Interface for the Response Object.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  HttpResponse
 */
interface HttpResponseInterface
{
    // Output Methods
    public static function setStatusCode($statusCode);
    public static function addHeader($name, $value);
    public static function setContent($content, $replace = false);
    public static function sendResponse();

    // Cookie Methods
    public static function createCookie(
        $name,
        $value = '',
        $maxage = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $HTTPOnly = false
    );

    public static function deleteCookie($name, $path = '/', $domain = '', $secure = false, $httponly = null);
}
