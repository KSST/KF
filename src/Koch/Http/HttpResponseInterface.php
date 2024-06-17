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

namespace Koch\Http;

/**
 * Interface for the Response Object.
 */
interface HttpResponseInterface
{
    // Output Methods

    /**
     * @param string $statusCode
     */
    public static function setStatusCode($statusCode);

    /**
     * @param string $name
     * @param string $value
     */
    public static function addHeader($name, $value);

    /**
     */
    public static function setContent($content, $replace = false);

    /**
     * @return bool
     */
    public static function sendResponse();

    // Cookie Methods

    /**
     * @return bool
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
     */
    public static function deleteCookie($name, $path = '/', $domain = '', $secure = false, $httponly = null);
}
