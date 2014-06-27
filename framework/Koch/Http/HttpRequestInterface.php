<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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
 * Koch Framework - Interface for the Request Object.
 */
interface HttpRequestInterface
{
    // Parameters

    /**
     * @param string $parameter
     *
     * @return string
     */
    public function issetParameter($parameter, $array = 'POST');
    public function getParameter($parameter, $array = 'POST');

    /**
     * @param string $array
     *
     * @return void
     */
    public function expectParameter($parameter, $array);

    /**
     * @return void
     */
    public function expectParameters(array $parameters);

    /**
     * @return string
     */
    public static function getHeader($parameter);

    // Direct Access to individual Parameters Arrays
    public function getGet();
    public function getPost();
    public function getCookies();
    public function getServer();

    // Request Method

    /**
     * @return string
     */
    public static function getRequestMethod();

    /**
     * @return void
     */
    public static function setRequestMethod($method);

    /**
     * @return boolean
     */
    public static function isAjax();

    // $_SERVER Stuff

    /**
     * @return string
     */
    public static function getServerProtocol();

    /**
     * @return boolean
     */
    public static function isSecure();

    /**
     * @return string
     */
    public static function getRemoteAddress();
}
