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

use Koch\Mvc\Mapper;
use Koch\Http\HttpRequest;

/**
 * Router_TargetRoute (processed RequestObject)
 */
class TargetRoute extends Mapper
{
    public static $parameters = array(
        // File
        'filename'      => null,
        'classname'     => null,
        // Call
        'module'        => 'index',
        'controller'    => null,
        'action'        => 'list',
        'method'        => null,
        'params'        => null,
        // Output
        'format'        => 'html',
        'language'      => 'en',
        'request'       => 'get',
        'layout'        => true,
        'ajax'          => false,
        'renderer'      => 'smarty',
        'themename'     => null,
        'modrewrite'    => false
    );

    /**
     * Get singleton instance of TargetRoute.
     *
     * @return \Koch\Router\TargetRoute
     */
    public static function instantiate()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self;
        }

        return $instance;
    }

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
            self::setFilename($filename);
        }

        return self::$parameters['filename'];
    }

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
        self::$parameters['controller'] = ucfirst($controller);
    }

    /**
     * Returns Name of the Controller
     *
     * @return string Controller/Modulename
     */
    public static function getController()
    {
        // the default "controller" name is the "module" name
        // this is the case if a route "/:module" is used
        if (null === self::$parameters['controller']) {
            self::$parameters['controller'] = self::$parameters['module'];
        }

        return ucfirst(self::$parameters['controller']);
    }

    public static function getModule()
    {
        return ucfirst(self::$parameters['module']);
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
     * Method to get the Action with Prefix
     *
     * @return $string
     */
    public static function getActionName()
    {
        return self::$parameters['method'];
    }

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
            // php5.4
            // $params = (new HttpRequest())->getPost();

            $request = new HttpRequest;
            $params = $request->getPost();

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

    public static function setRenderEngine($renderEngineName)
    {
        self::$parameters['renderer'] = $renderEngineName;
    }

    public static function getBackendTheme()
    {
        return isset($_SESSION['user']['backend_theme']) === true ? $_SESSION['user']['backend_theme'] : 'admin';
    }

    public static function getFrontendTheme()
    {
        return isset($_SESSION['user']['frontend_theme']) === true  ? $_SESSION['user']['frontend_theme'] : 'standard';
    }

    public static function getThemeName()
    {
        if (null === self::$parameters['themename']) {
            // set theme automatically for "main backend module" or "backend controllers"
            if (self::getModule() == 'Controlcenter' or self::getController() == 'admin') {
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
     * @return boolean True if TargetRoute is dispatchable, false otherwise.
     */
    public static function dispatchable()
    {
        $file = self::getFilename();
        $class = self::getClassname();
        $method = self::getMethod();

        // was the class loaded before?
        if (false === class_exists($class, false)) {
            // not... then try loading manually
            if (is_file($file) === true) {
                include_once $file;
            }
        }

        // class is loaded, check if method exists
        if (true === class_exists($class, false) and true === method_exists($class, $method)) {
                return true;
        }

        // this shows how many routes were tried
        #echo '<br><strong>Route failure.<br>'.CR;
        #echo 'Not found [ ' . $file .' | '. $class .' | '. $method . ' ]</strong><br>'.CR;

        return false;
    }

    /**
     * setSegmentsToTargetRoute
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
        if (array_key_exists('requirements', $array)) {
            $array = $array['requirements'];
        }

        // Module
        if (isset($array['module']) === true) {
            self::setModule($array['module']);
            // yes, set the controller of the module, too
            // if it is e.g. AdminController on Module News, then it will be overwritten below
            self::setController($array['module']);
            unset($array['module']);
        }

        // Controller
        if (isset($array['controller']) === true) {
            self::setController($array['controller']);
            // if a module was not set yet, then set the current controller also as module
            if (self::$parameters['module'] === 'index') {
                self::setModule($array['controller']);
            }
            unset($array['controller']);
        }

        // Action
        if (isset($array['action']) === true) {
            self::setAction($array['action']);
            unset($array['action']);
        }

        // Id
        if (isset($array['id']) === true) {
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
        $reset_params = array(
            // File
            'filename' => null,
            'classname' => null,
            // Call
            'module' => 'index',
            'controller' => 'index',
            'action' => 'list',
            'method' => 'actionList',
            'params' => null,
            // Output
            'format' => 'html',
            'language' => 'en',
            'request' => 'get',
            'layout' => true,
            'ajax' => false,
            'renderer' => 'smarty',
            'themename' => null,
            'modrewrite' => false
        );

        #self::$parameters = array_merge(self::$parameters, $reset_params);
        self::$parameters = $reset_params;
    }

    public static function getRoute()
    {
        return self::$parameters;
    }

    public static function debug()
    {
        \Koch\Debug\Debug::printR(self::$parameters);
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
