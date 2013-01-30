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
    public function deleteRoutesOfModule($module)
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
        // load application wide routes configuration file
        if ($module === '') {
            $file = APPLICATION_PATH . 'configuration/routes.php';
        } else {
            // load module specific routes config file
            $file = APPLICATION_MODULES_PATH . $module . '/' . $module . '.routes.php';
        }

        if (is_file($file) === true) {
            return include $file;
        }
    }
}
