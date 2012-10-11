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

namespace Koch\Router;

/**
 * Koch FrameworkRouter Management
 *
 * On Installation
 *      new routes are added via the method addRoutesOfModule($modulename).
 * On Deinstallation
 *      the routes are removed via method delRoutesOfModule($modulename).
 */
class Manager
{
    public function addRoutesOfModule($modulename)
    {
        self::updateApplicationRoutes($modulename);
    }

    public function delRoutesOfModule($modulename)
    {
        // @todo
        $module_routes_file = APP_MODULES_DIR . $modulename . '/' . $modulename . '.routes.php';
        $module_routes = $this->loadRoutesFromConfig($module_routes_file);

        // load main routes file
        $application_routes = $this->loadRoutesFromConfig();

        // subtract the $module_routes from $application_routes array
        $this->deleteRoute($route_name);

        // update / write merged content to application config

    }

    public function deleteRoute($route_name)
    {
        $routes_count = count($this->routes);

        // loop over all routes
        for ($i == 0; $i < $routes_count; $i++) {
            // check if there is a route with the given name
            if ($this->routes[$i]['name'] == $route_name) {
                // got one? then remove it from the routes array and stop
                array_splice($this->routes, $i, 1);
                break;
            }
        }

        return $this->routes;
    }

    /**
     * Registers routing for all activated modules
     *
     * @param string $modulename Name of module
     */
    public function updateApplicationRoutes($modulename = null)
    {
        $activated_modules = array();

        if ($modulename === null) {
            $activated_modules[] = array($modulename);
        } else { // get all activated modules
            // $activated_modules =
        }

        foreach ($activated_modules as $modulename) {
            // load module routing file
            $module_routes_file = APP_MODULES_DIR . $modulename . '/' . $modulename . '.routes.php';
            $module_routes = $this->loadRoutesFromConfig($module_routes_file);

            // load main routes file
            $application_routes = $this->loadRoutesFromConfig();

            // merge the content of modules into application
            // @todo: consider using array_merge_recursive_distinct /unique ?
            $combined_routes = array_merge_recursive($module_routes, $application_routes);

            // update / write merged content to application config
        }
    }

    /**
     * Load Routes from any Route Configuration File
     *
     * @param string Path to a (module) Routing Configuration File.
     * @return array Array of Routes.
     */
    public static function loadRoutesFromConfig($routes_config_file = null)
    {
        $routes = array();

        if ($routes_config_file === null) {
            // load common routes configuration
            $routes = include ROOT_CONFIG . 'routes.php';
        } else {
            // load specific routes config file
            $routes = include ROOT . $routes_config_file;
        }

        return $routes;
    }
}
