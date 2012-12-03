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
 *
 */

namespace Koch\Http;

/**
 * Koch Framework - Interface for the Request Object.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  HttpRequest
 */
interface HttpRequestInterface
{
    // Parameters
    public function issetParameter($parameter, $array = 'POST');
    public function getParameter($parameter, $array = 'POST');
    public function expectParameter($parameter, $array);
    public function expectParameters(array $parameters);
    public static function getHeader($parameter);

    // Direct Access to individual Parameters Arrays
    public function getGet($parameter);
    public function getPost($parameter);
    public function getCookie($parameter);
    public function getServer($parameter);

    // Request Method
    public static function getRequestMethod();
    public static function setRequestMethod($method);
    public static function isAjax();

    // $_SERVER Stuff
    public static function getServerProtocol();
    public static function isSecure();
    public static function getRemoteAddress();
}
