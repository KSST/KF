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
    public function issetParameter($name, $arrayname = 'POST');
    public function getParameter($name, $arrayname = 'POST');
    public function expectParameter($parameter, $arrayname);
    public function expectParameters(array $parameters);
    public static function getHeader($name);

    // Direct Access to individual Parameters Arrays
    public function getParameterFromCookie($name);
    public function getParameterFromGet($name);
    public function getParameterFromPost($name);
    public function getParameterFromServer($name);

    // Request Method
    public static function getRequestMethod();
    public static function setRequestMethod($method);
    public static function isAjax();

    // $_SERVER Stuff
    public static function getServerProtocol();
    public static function isSecure();
    public static function getRemoteAddress();
}
