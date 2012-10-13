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
    /**
     * Add Routes of a Module to the Main Routes Config.
     *
     * @param type $module
     */
    public function addRoutesOfModule($module)
    {
        self::updateApplicationRoutes($module);
    }

    /**
     * Remove Routes of a Module from Main Routes Config.
     *
     * @param type $module
     */
    public function delRoutesOfModule($module)
    {
        // load module routes
        $moduleRoutes = $this->loadRoutesFromConfig($module);

        // load main routes file
        $applicationRoutes = $this->loadRoutesFromConfig();

        // @todo subtract the $module_routes from $application_routes array
        //$this->deleteRoute($route_name);

        // update / write merged content to application config

    }

    /**
     * Delete a specific route.
     *
     * @param  type  $route_name
     * @return array Routes.
     */
    public function deleteRoute($route_name)
    {
        $routesCount = count($this->routes);

        // loop over all routes
        for ($i == 0; $i < $routesCount; $i++) {
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
     * Registers routing for all activated modules.
     *
     * @param string $module Name of module
     */
    public function updateApplicationRoutes($module = null)
    {
        $activatedModules = array();

        if ($module === null) {
            $activatedModules[] = array($module);
        } else { // get all activated modules
            // $activated_modules =
        }

        foreach ($activatedModules as $module) {
            // load module routing file
            $moduleRoutes = self::loadRoutesFromConfig($module);

            // load main routes file
            $applicationRoutes = self::loadRoutesFromConfig();

            // merge the content of modules into application
            // @todo: consider using array_merge_recursive_distinct /unique ?
            $combinedRoutes = array_merge_recursive($moduleRoutes, $applicationRoutes);

            // update / write merged content to application config
        }
    }

    /**
     * Load Routes from any Route Configuration File
     *
     * @param string Name of a module. Default: main routes config.
     * @return array Array of Routes.
     */
    public static function loadRoutesFromConfig($module = '')
    {
        // load common routes configuration
        if ($module === '') {
            return include APPLICATION_PATH . 'configuration/routes.php';
        }

        // load a module specific routes config file
        return include APP_MODULES_DIR . $module . '/' . $module . '.routes.php';
    }
}
