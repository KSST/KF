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
 * Interface for the Request Object.
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

    // Request Method
    public static function getRequestMethod();
    public static function setRequestMethod($method);
    public static function isAjax();

    // $_SERVER Stuff
    public static function getServerProtocol();
    public static function isSecure();
    public static function getRemoteAddress();
}
