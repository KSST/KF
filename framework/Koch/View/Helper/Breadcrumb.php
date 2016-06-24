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

namespace Koch\View\Helper;

use Koch\Router\TargetRoute;

/**
 * Class for Breadcrumb Handling.
 */
class Breadcrumb
{
    /**
     * @var array contains the complete path structured as array
     */
    private static $path = [];

    /**
     * Adds a new breadcrumb.
     *
     * @param string $title                  Name of the trail element
     * @param string $link                   Link of the trail element
     * @param string $replace_array_position Position in the array to replace with name/trail. Start = 0.
     */
    public static function add($title, $link = '', $replace_array_position = null)
    {
        // set data to breadcrumb
        $breadcrumb = [
            'title' => ucwords($title),
            'link'  => '/' . ltrim($link, '/ '),
        ];

        // replace
        if ($replace_array_position !== null) {
            self::$path[$replace_array_position] = $breadcrumb;
        } else { // just add
            self::$path[] = $breadcrumb;
        }

        unset($breadcrumb);
    }

    /**
     * Replace is a convenience method for add.
     * Remembering you that you might want to replace existing breadcrumbs.
     *
     * @param string $title                  Name of the trail element
     * @param string $link                   Link of the trail element
     * @param string $replace_array_position Position in the array to replace with name/trail. Start = 0.
     */
    public static function replace($title, $link = '', $replace_array_position = null)
    {
        self::add($title, $link, $replace_array_position);
    }

    /**
     * Adds breadcrumbs dynamically based on current module, submodule and action.
     * This might look a bit rough to the user.
     * Please prefer adding breadcrumbs manually via add().
     */
    public static function addDynamicBreadcrumbs()
    {
        $module     = strtolower(TargetRoute::getModule());
        $controller = strtolower(TargetRoute::getController());
        $action     = TargetRoute::getActionNameWithoutPrefix();

        if (isset($module) and $module !== 'controlcenter') {
            $url = 'index.php?mod=' . $module;

            // Level 2
            // do not add ctrl part, if controller and module are the same
            if ($controller !== '' and $controller !== $module) {
                $url .= '&amp;ctrl=' . $controller;
                $module .= ' ' . $controller;
            }
            self::add($module, $url);

            // Level 3
            if ($action !== '') {
                $url .= '&amp;action=' . $action;
                self::add($action, $url);
            }
        }
    }

    /**
     * Getter for the breadcrumbs/trail array.
     *
     * @param bool $dynamic_add If true, adds the breadcrumbs dynamically (default), otherwise just returns.
     *
     * @return array self::$path The breadcrumbs array.
     */
    public static function getTrail($dynamic_add = true)
    {
        // if we got only one breadcrumb element, then only Home or ControlCenter was set before
        if (count(self::$path) === 1 and $dynamic_add === true) {
            // add crumbs automatically
            self::addDynamicBreadcrumbs();
        }

        return self::$path;
    }

    /**
     * Breadcrumb Level 0    =>    Home or Controlcenter.
     *
     * @param string $module     The module name.
     * @param string $controller The controller name.
     */
    public static function initialize($module = null, $controller = null)
    {
        // ControlCenter (Backend)
        if ($module === 'controlcenter' or $controller === 'admin') {
            self::add('Control Center', '/index.php?mod=controlcenter');
        } else { // Home (Frontend)
            self::add('Home');
        }
    }

    /**
     * Resets the breadcrumbs array.
     */
    public static function resetBreadcrumbs()
    {
        self::$path = [];
    }
}
