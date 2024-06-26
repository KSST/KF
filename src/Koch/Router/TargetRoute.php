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

use Koch\Http\HttpRequest;
use Koch\Mvc\Mapper;

/**
 * Router_TargetRoute (processed RequestObject).
 */
class TargetRoute extends Mapper
{
    public static $parameters = [
        // File
        'filename'  => null,
        'classname' => null,
        // Call
        'module'     => 'index',
        'controller' => null,
        'action'     => 'list',
        'method'     => null,
        'params'     => [],
        // Output
        'format'     => 'html',
        'language'   => 'en',
        'request'    => 'get',
        'layout'     => true,
        'ajax'       => false,
        'renderer'   => 'smarty',
        'themename'  => 'default',
        'modrewrite' => false,
    ];

    /**
     * Get singleton instance of TargetRoute.
     *
     * @return \Koch\Router\TargetRoute
     */
    public static function instantiate()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public static function getApplicationNamespace()
    {
        return Mapper::getApplicationNamespace();
    }

    /**
     * @param string $filename
     */
    public static function setFilename($filename)
    {
        self::$parameters['filename'] = $filename;
    }

    public static function getFilename()
    {
        if (empty(self::$parameters['filename'])) {
            $filename = self::mapControllerToFilename(
                self::getModulePath(self::getModule()),
                self::getController()
            );
            self::setFilename(realpath($filename));
        }

        return self::$parameters['filename'];
    }

    /**
     * @param string $classname
     */
    public static function setClassname($classname)
    {
        self::$parameters['classname'] = $classname;
    }

    public static function getClassname()
    {
        if (empty(self::$parameters['classname'])) {
            $classname = self::mapControllerToClassname(self::getModule(), self::getController());
            self::setClassname($classname);
        }

        return self::$parameters['classname'];
    }

    public static function setController($controller)
    {
        self::$parameters['controller'] = ucfirst((string) $controller);
    }

    /**
     * Returns Name of the Controller.
     *
     * @return string Controller/Modulename
     */
    public static function getController()
    {
        // the default "controller" name is the "module" name
        // this is the case if a route "/:module", e.g. "/users" is used
        if (empty(self::$parameters['controller'])) {
            self::$parameters['controller'] = self::$parameters['module'];
        }

        return ucfirst((string) self::$parameters['controller']);
    }

    public static function getModule()
    {
        return ucfirst((string) self::$parameters['module']);
    }

    public static function setModule($module)
    {
        self::$parameters['module'] = $module;
    }

    public static function setAction($action)
    {
        self::$parameters['action'] = $action;
    }

    public static function getAction()
    {
        return self::$parameters['action'];
    }

    public static function getActionNameWithoutPrefix()
    {
        $action = str_replace('action', '', self::$parameters['action']);

        return $action;
    }

    public static function setId($id)
    {
        self::$parameters['params']['id'] = $id;
    }

    public static function getId()
    {
        return self::$parameters['params']['id'];
    }

    /**
     * Method to get the Action with Prefix.
     *
     * @return $string
     */
    public static function getActionName()
    {
        return self::$parameters['method'];
    }

    /**
     * @param string $method
     */
    public static function setMethod($method)
    {
        self::$parameters['method'] = $method;
    }

    public static function getMethod()
    {
        // add method prefix (action_)
        $method = self::mapActionToMethodname(self::getAction());
        self::setMethod($method);

        return self::$parameters['method'];
    }

    public static function setParameters($params)
    {
        self::$parameters['params'] = $params;
    }

    public static function getParameters()
    {
        // transfer parameters from HttpRequest Object to TargetRoute
        if (HttpRequest::getRequestMethod() === 'POST') {
            $request = new HttpRequest();
            $params  = $request->getPost();

            self::setParameters($params);
        }

        return self::$parameters['params'];
    }

    public static function getFormat()
    {
        return self::$parameters['format'];
    }

    public static function getRequestMethod()
    {
        return HttpRequest::getRequestMethod();
    }

    public static function getLayoutMode()
    {
        return (bool) self::$parameters['layout'];
    }

    public static function getAjaxMode()
    {
        return HttpRequest::isAjax();
    }

    public static function getRenderEngine()
    {
        return self::$parameters['renderer'];
    }

    /**
     * @param string $renderEngineName
     */
    public static function setRenderEngine($renderEngineName)
    {
        self::$parameters['renderer'] = $renderEngineName;
    }

    public static function getBackendTheme()
    {
        return $_SESSION['user']['backend_theme'] ?? 'default';
    }

    public static function getFrontendTheme()
    {
        return $_SESSION['user']['frontend_theme'] ?? 'default';
    }

    public static function getThemeName()
    {
        if (null === self::$parameters['themename']) {
            // switch automatically to a backend theme
            // in case the "Control Center" or "Backend Controller" is requested
            if (self::getModule() === 'Controlcenter' or self::getController() === 'Admin') {
                self::setThemeName(self::getBackendTheme());
            } else {
                self::setThemeName(self::getFrontendTheme());
            }
        }

        return self::$parameters['themename'];
    }

    public static function setThemeName($themename)
    {
        self::$parameters['themename'] = $themename;
    }

    public static function getModRewriteStatus()
    {
        return (bool) self::$parameters['modrewrite'];
    }

    /**
     * Dispatchable ensures that the "logical" route is "physically" valid.
     * The method checks, if the TargetRoute relates to correct file, controller and action.
     *
     * @return bool True if TargetRoute is dispatchable, false otherwise.
     */
    public static function dispatchable()
    {
        $class  = self::getClassname();
        $file   = self::getFilename();
        $method = self::getMethod();

        // prevent redeclaration, by checking "method in class" (implicit class_exist())
        if (method_exists($class, $method)) {
            return true;
        }

        // trigger autoload & check for "class in file / PSR-0" and "method in class"
        if (true === class_exists($class) and true === method_exists($class, $method)) {
            return true;
        }

        // LEAVE THIS - It shows how many routes were tried before a match happens!
        //#\Koch\Debug\Debug::firebug('Route not found : [ ' . $file .' | '. $class .' | '. $method . ']');
        return false;
    }

    /**
     * setSegmentsToTargetRoute.
     *
     * This takes the requirements array or the uri_segments array
     * and sets the proper parameters on the Target Route,
     * thereby making it dispatchable.
     *
     * URL Examples
     * a) index.php?mod=news=action=archive
     * b) index.php?mod=news&ctrl=admin&action=edit&id=77
     *
     * mod      => controller => <News>Controller.php
     * ctrl     => controller suffix  => News<Admin>Controller.php
     * action   => method     => action_<action>
     * *id*     => additional call params for the method
     */
    public static function setSegmentsToTargetRoute($array)
    {
        /**
         * if array is an found route, it has the following array structure:
         * [regexp], [number_of_segments] and [requirements].
         *
         * for getting the values module, controller, action only the
         * [requirements] array is relevant. overwriting $array drops the keys
         * [regexp] and [number_of_segments] because they are no longer needed.
         */
        if ((isset($array['requirements'])) || (array_key_exists('requirements', $array))) {
            $array = $array['requirements'];
        }

        // Module
        if (isset($array['module'])) {
            self::setModule($array['module']);
            // yes, set the controller of the module, too
            // if it is e.g. AdminController on Module News, then it will be overwritten below
            self::setController($array['module']);
            unset($array['module']);
        }

        // Controller
        if (isset($array['controller'])) {
            self::setController($array['controller']);
            // if a module was not set yet, then set the current controller also as module
            if (self::$parameters['module'] === 'index') {
                self::setModule($array['controller']);
            }
            unset($array['controller']);
        }

        // Action
        if (isset($array['action'])) {
            self::setAction($array['action']);
            unset($array['action']);
        }

        // Id
        if (isset($array['id'])) {
            self::setId($array['id']);

            // if we set an ID and the action is still empty (=default: list),
            // then we automatically set the action name according to the request method
            if (self::$parameters['action'] === 'list') {
                $request_method = self::getRequestMethod();

                if ($request_method === 'GET') {
                    self::setAction('show');
                } elseif ($request_method === 'PUT') {
                    self::setAction('update');
                } elseif ($request_method === 'DELETE') {
                    self::setAction('delete');
                }
            }

            unset($array['id']);
        }

        // if the request method is POST then set the action INSERT
        if ('POST' === self::getRequestMethod()) {
            self::setAction('insert');
        }

        // Parameters
        if (count($array) > 0) {
            self::setParameters($array);
            unset($array);
        }

        # instantiate the target route

        return self::instantiate();
    }

    public static function reset()
    {
        $reset_params = [
            // File
            'filename'  => null,
            'classname' => null,
            // Call
            'module'     => 'index',
            'controller' => 'index',
            'action'     => 'list',
            'method'     => 'actionList',
            'params'     => [],
            // Output
            'format'     => 'html',
            'language'   => 'en',
            'request'    => 'get',
            'layout'     => true,
            'ajax'       => false,
            'renderer'   => 'smarty',
            'themename'  => 'default',
            'modrewrite' => false,
        ];

        self::$parameters = $reset_params;
    }

    public static function getRoute()
    {
        return self::$parameters;
    }
}
