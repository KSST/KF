<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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

namespace Koch\Mvc;

use Koch\Filter\FilterInterface; // @todo move usage into prefilter
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\View\Helper\Breadcrumb;

/**
 * Koch Framework FrontController.
 *
 * It's basically a FrontController (which should better be named RequestController)
 * with fassade to both filtermanagers (addPreFilter, addPostFilter) on top.
 *
 * It's tasks are:
 * 1. to intercept all requests made by the client to the web server through central "index.php".
 * 2. to get all needed "pre action processing" done; things like Auth, Sessions, Logging, whatever... pluggable or not.
 * 3. to decide then, which ModuleController we must dynamically invoking to process the request.
 */
class FrontController implements FrontControllerInterface
{
    /**
     * @var object \Koch\Http\HttpRequest
     */
    private $request;

    /**
     * @var object \Koch\Http\HttpResponse
     */
    private $response;

    /**
     * @var object \Koch\Router\Router
     */
    private $router;

    /**
     * @var object \Koch\Filter\FilterManager for Prefilters
     */
    private $preFilterManager;

    /**
     * @var object \Koch\Filter\FilterManager for Postfilters
     */
    private $postFilterManager;

    /**
     * @var object \Koch\Event\Dispatcher
     */
    private $eventDispatcher;

    /**
     * Constructor.
     */
    public function __construct(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $this->request           = $request;
        $this->response          = $response;
        $this->preFilterManager  = new \Koch\Filter\FilterManager();
        $this->postFilterManager = new \Koch\Filter\FilterManager();
        $this->eventDispatcher   = \Koch\Event\Dispatcher::instantiate();
        $this->router            = new \Koch\Router\Router($this->request);
    }

    /**
     * Add a Prefilter.
     *
     * This filter is processed *before* the Controller->Action is executed.
     *
     * @param FilterInterface $filter Object implementing the Koch_Filter_Interface.
     */
    public function addPreFilter(FilterInterface $filter)
    {
        $this->preFilterManager->addFilter($filter);
    }

    /**
     * Add a Postfilter.
     *
     * This filter is processed *after* Controller->Action was executed.
     *
     * @param FilterInterface $filter Object implementing the Koch_Filter_Interface.
     */
    public function addPostFilter(FilterInterface $filter)
    {
        $this->postFilterManager->addFilter($filter);
    }

    /**
     * Front_Controller::processRequest() = dispatch().
     *
     * Speaking in very basic concepts: this is a RequestHandler.
     * The C in MVC. It handles the dispatching of the request.
     * It calls the apropriate controller and returns a response.
     */
    public function processRequest()
    {
        $this->router->route();

        #$this->preFilterManager->processFilters($this->request, $this->response);

        $this->eventDispatcher->triggerEvent('onBeforeDispatcherForward');

        $this->forward();

        $this->eventDispatcher->triggerEvent('onAfterDispatcherForward');

        $this->postFilterManager->processFilters($this->request, $this->response);

        $this->response->sendResponse();
    }

    /**
     * The dispatcher accepts the found route from the route mapper and
     * invokes the correct controller and method.
     *
     * Workflow
     * 1. fetches Route Object
     * 2. extracts info about correct controller, correct method with correct parameters
     * 3. tries to call the method "initializeModule" on the controller
     * 4. finally tries to call the controller with method(params)!
     *
     * The dispatcher forwards to the pagecontroller = modulecontroller + moduleaction.
     */
    public function forward()
    {
        $route = $this->request->getRoute();

        $classname  = $route::getClassname();
        $method     = $route::getMethod();
        $parameters = $route::getParameters();
        #$request_meth = \Koch\Http\HttpRequest::getRequestMethod();
        #$renderengine = $route::getRenderEngine();

        #$this->eventDispatcher->addEventHandler('onBeforeControllerMethodCall', new Koch\Event\InitializeModule());

        #\Koch\Debug\Debug::firebug($classname . ' ' . $method . ' ' . var_export($parameters, true));

        $controllerInstance = new $classname($this->request, $this->response);

        /*
         * Initialize the Module
         *
         * by calling the "_initializeModule" method on the controller.
         * A module might(!) implement this method for initialization of helper objects.
         * Basically it's a constructor! Keep it lightweight!
         *
         * Note the underscore! The method name is intentionally underscored.
         * This places the method on top in the method navigator of your IDE.
         */
        if (method_exists($controllerInstance, '_initializeModule')) {
            $controllerInstance->_initializeModule();
        }

        /*
         * "Before Module Filter" is a prefilter on the module controller level.
         *
         * It calls the "_beforeFilter" method on the module controller.
         * A module might(!) implement this method for initialization of helper objects.
         * Example usage: login_required.
         *
         * Note the underscore! The method name is intentionally underscored.
         * This places the method on top in the method navigator of your IDE.
         */
        if (method_exists($controllerInstance, '_beforeModuleFilter')) {
            $controllerInstance->_beforeModeFilter();
        }

        // @todo auto-attach a Module::onBootstrap method as event

        // @todo move into a prefilter / and consider the request being ajax = no breadcrumbs
        Breadcrumb::initialize($route->getModule(), $route->getController());

        /*
         * Finally: dispatch to the requested controller method
         */
        if (method_exists($controllerInstance, $method)) {
            $controllerInstance->$method($parameters);
        } else {
            echo 'Class ' . $classname . '->Method ' . $method . ' not found.';
        }

         /*
         * "After Module Filter" is a postfilter on the module controller level.
         *
         * It calls the "_afterFilter" method on the module controller.
         * A module might(!) implement this method for running further processing
         * on reponse data.
         *
         * Note the underscore! The method name is intentionally underscored.
         * This places the method on top in the method navigator of your IDE.
         */
        if (method_exists($controllerInstance, '_afterModuleFilter')) {
            $controllerInstance->_afterModuleFilter();
        }

        // @todo auto-attach a Module::onShutdown method as event
    }
}
