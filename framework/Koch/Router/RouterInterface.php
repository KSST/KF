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

namespace Koch\Router;

/**
 * Interface for Router(s).
 *
 * A router has to implement the following methods to resolve the Request to a Module and the Action/Command.
 */
interface RouterInterface
{
    /**
     */
    public function addRoute($url_pattern, array $route_options = null);

    /**
     */
    public function addRoutes(array $routes);
    public function getRoutes();

    /**
     */
    public function delRoute($name);

    /**
     * @return string
     */
    public function generateURL($url_pattern, array $params = null, $absolute = false);

    /**
     * @return TargetRoute|null
     */
    public function route();
}
