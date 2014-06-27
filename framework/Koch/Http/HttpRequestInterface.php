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
