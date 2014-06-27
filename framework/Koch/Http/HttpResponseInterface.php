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
 */
interface HttpResponseInterface
{
    // Output Methods

    /**
     * @param string $statusCode
     *
     * @return void
     */
    public static function setStatusCode($statusCode);

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public static function addHeader($name, $value);

    /**
     * @return void
     */
    public static function setContent($content, $replace = false);

    /**
     * @return boolean
     */
    public static function sendResponse();

    // Cookie Methods

    /**
     * @return boolean
     */
    public static function setCookie(
        $name,
        $value = '',
        $maxage = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $HTTPOnly = false
    );

    /**
     * @return void
     */
    public static function deleteCookie($name, $path = '/', $domain = '', $secure = false, $httponly = null);
}
