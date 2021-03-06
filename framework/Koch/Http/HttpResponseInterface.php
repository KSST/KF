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

namespace Koch\Http;

/**
 * Koch Framework - Interface for the Response Object.
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
