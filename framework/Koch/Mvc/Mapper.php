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

namespace Koch\Mvc;

/**
 * Mapper.
 *
 * Provides helper methods to transform (map)
 * (a) the controller name into the specific application classname and filename
 * (b) the action name into the specific application actioname.
 */
class Mapper extends \ArrayObject
{
    /* @const string Classname prefix for modules */
    const MODULE_NAMESPACE = '\Modules';

    /* @const string suffix for module controller files */
    const MODULE_CLASS_SUFFIX = 'Controller.php';

    /* @const string Method prefix for module actions */
    const ACTION_PREFIX = 'action';

    /* @const string Name of the Default Module */
    const DEFAULT_MODULE = 'index';

    /* @const string Name of the Default Action */
    const DEFAULT_ACTION = 'index';

    public static $applicationNamespace = '';

    /**
     * Set the application namespace.
     * Usage: setApplicationNamespace(__NAMESPACE__);.
     *
     * @param string $namespace
     */
    public static function setApplicationNamespace($namespace)
    {
        $namespace                  = ltrim($namespace, '\\');
        self::$applicationNamespace = '\\' . $namespace;
    }

    public static function getApplicationNamespace()
    {
        return self::$applicationNamespace;
    }

    /**
     * @param string $module
     */
    public static function getModulePath($module)
    {
        return APPLICATION_MODULES_PATH . $module . '/';
    }

    /**
     * Maps the controller and subcontroller (optional) to filename.
     *
     * @param string $module_path Path to Module
     * @param string $controller  Name of Controller
     *
     * @return string filename
     */
    public static function mapControllerToFilename($module_path, $controller = null)
    {
        // append "Controller" sub-folder to module_path
        $module_path .= 'Controller/';

        // Mapping Example:
        // "/Modules/News/Controller/" + "Index" + "Controller.php"
        return $module_path . ucfirst($controller) . self::MODULE_CLASS_SUFFIX;
    }

    /**
     * Maps Controller to Classname.
     *
     * @param string $module     Name of Module
     * @param string $controller Name of Controller (optional)
     *
     * @return string classname
     */
    public static function mapControllerToClassname($module, $controller = '')
    {
        $classname = '\\';

        if ($controller === '') {
            // the default controller of a module is named like the module
            // The module "News"  has a controller named "News"Controller.
            $controller = $module;
        }

        /*
         * (<Modulename|DefaultController>\Controller\<Controller>Controller)
         * e.g. "News\Controller\NewsController"
         */
        $classname .= $module . '\Controller\\' . $controller . 'Controller';

        // "Application" + "\Modules" + "\News\Controller\NewsController"
        return self::$applicationNamespace . self::MODULE_NAMESPACE . $classname;
    }

    /**
     * Maps the action to it's method name taking controller into account.
     *
     * The prefix 'action' (pseudo-namespace) is used for all actions.
     * Example: A action named "show" will be mapped to "actionShow()"
     * This is also a way to ensure some kind of whitelisting via namespacing.
     *
     * The convention is action<action> !
     *
     *
     * @param string $action the action
     *
     * @return string the mapped method name
     */
    public static function mapActionToMethodname($action = null)
    {
        // set default value for action, when not set by URL
        if ($action === null) {
            $action = self::DEFAULT_ACTION;
        }

        // all application actions are prefixed with 'action'
        // e.g. action_<login>
        return self::ACTION_PREFIX . ucfirst($action);
    }
}