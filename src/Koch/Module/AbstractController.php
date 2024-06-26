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

namespace Koch\Module;

use Koch\Http\HttpRequest;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * ModuleController.
 *
 * The ModuleController is an abstract class (parent class)
 * to share some common features on/for all (Module/Action)-Controllers.
 * It`s abstract, because it should only be extended, not instantiated.
 */
abstract class AbstractController
{
    /**
     * @var object The rendering engine / view object
     */
    public $view;

    /**
     * @var string Name of the rendering engine
     */
    public $renderEngineName;

    /**
     * @var string The name of the template to render
     */
    public $template;

    /**
     * @var \Koch\Http\HttpResponse
     */
    public $response;

    /**
     * @var \Koch\Http\HttpRequest
     */
    public $request;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    public $entityManager;

    /**
     * @var array The Module Configuration Array
     */
    public static $moduleconfig;

    public function __construct(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Returns the Doctrine Entity Manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    /**
     * The name of the entity extracted from the classname.
     *
     * @param string Classname
     *
     * @return string The name of the entity extracted from classname
     */
    public function getEntityNameFromClassname()
    {
        $matches = [];

        // takes classname, e.g. "Application\Modules\News\Controller\NewsController"
        $class = static::class;
        preg_match("~Controller\\\(.*)Controller~is", $class, $matches);

        // and returns the entity name, e.g. "Entity\News"
        $this->entityName = 'Entity\\' . $matches[1];

        return $this->entityName;
    }

    /**
     * Proxy/Convenience Getter Method for the Repository of the current Module.
     *
     *
     * @param string $entityName Name of an Entity, like "\Entity\User".
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getModel($entityName = null)
    {
        if (null === $entityName) {
            $entityName = $this->getEntityNameFromClassname();
        }

        return $this->entityManager->getRepository($entityName);
    }

    /**
     * Saves this and all others models (calls persist + flush)
     * Save (save one)
     * Flush (save all).
     *
     * @param \Doctrine\ORM\Mapping\Entity $model Entity.
     * @param bool                         $flush Uses flush on true, save on false. Defaults to flush (true).
     */
    public function saveModel(\Doctrine\ORM\Mapping\Entity $model, $flush = true)
    {
        $this->entityManager->persist($model);

        if ($flush === true) {
            $this->entityManager->flush();
        } else {
            $this->entityManager->save();
        }
    }

    /**
     * Initializes the model (active records/entities/repositories) of the module.
     *
     * @param $modulename Modulname
     * @param $recordname Recordname
     */
    public static function setModel($modulename = null, $entity = null)
    {
        $module_models_path = '';

        /*
         * Load the Records for the current module, if no modulename is specified.
         * This is for lazy usage in the modulecontroller: $this->initModel();
         */
        if ($modulename === null) {
            $modulename = (new HttpRequest())->getRoute()->getModuleName();
        }

        $module_models_path = APPLICATION_MODULES_PATH . mb_strtolower((string) $modulename) . '/model/';

        // check if the module has a models dir
        if (is_dir($module_models_path)) {
            if ($entity !== null) {
                // use second parameter of method
                $entity = $module_models_path . 'Entities/' . ucfirst((string) $entity) . '.php';
            } else {
                // build entity filename by modulename
                $entity = $module_models_path . 'Entities/' . ucfirst((string) $modulename) . '.php';
            }

            if (is_file($entity) && class_exists('Entity\\' . ucfirst((string) $modulename), false)) {
                include $entity;
            }

            $repos = $module_models_path . 'Repositories/' . ucfirst((string) $modulename) . 'Repository.php';

            if (is_file($repos) && class_exists('Entity\\' . ucfirst((string) $modulename), false)) {
                include $repos;
            }
        }
        // else Module has no Model Data
    }

    /**
     * Gets a Module Config.
     *
     * @param string $modulename Modulename.
     *
     * @return array configuration array of module
     */
    public static function getModuleConfig($modulename = null)
    {
        $config = self::getInjector()->instantiate(\Koch\Config\Config::class);

        return self::$moduleconfig = $config->readModuleConfig($modulename);
    }

    /**
     * Gets a Config Value or sets a default value.
     *
     * @example
     * Usage for one default variable:
     * self::getConfigValue('items_newswidget', '8');
     * Gets the value for the key items_newswidget from the moduleconfig or sets the value to 8.
     *
     * Usage for two default variables:
     * self::getConfigValue('items_newswidget', $_GET['numberNews'], '8');
     * Gets the value for the key items_newswidget from the moduleconfig or sets the value
     * incomming via GET, if nothing is incomming, sets the default value of 8.
     *
     * @param string $keyname     The keyname to find in the array.
     *
     * @return mixed
     */
    public static function getConfigValue($keyname, mixed $default_one = null, mixed $default_two = null)
    {
        // if we don't have a moduleconfig array yet, get it
        if (self::$moduleconfig === null) {
            self::$moduleconfig = self::getModuleConfig();
        }

        // try a lookup of the value by keyname
        $value = \Koch\Functions\Functions::array_find_element_by_key($keyname, self::$moduleconfig);

        // return value or default
        if (empty($value) === false) {
            return $value;
        } elseif ($default_one !== null) {
            return $default_one;
        } elseif ($default_two !== null) {
            return $default_two;
        } else {
            return;
        }
    }

    /**
     * Get the dependency injector.
     *
     * @return Returns a static reference to the Dependency Injector
     */
    public static function getInjector()
    {
        return \Clansuite\Application::getInjector();
    }

    /**
     * Set view.
     *
     * @param object $view RenderEngine Object
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Get view returns the render engine.
     *
     * @param string $renderEngineName Name of the render engine, like smarty, phptal.
     *
     * @return Returns the View Object (Rendering Engine)
     */
    public function getView($renderEngineName = null)
    {
        // set the renderengine name
        if ($renderEngineName !== null) {
            $this->setRenderEngine($renderEngineName);
        }

        // if already set, get the rendering engine from the view variable
        if ($this->view !== null) {
            return $this->view;
        } else {
            // else, set the RenderEngine to the view variable and return it
            $this->view = $this->getRenderEngine();

            return $this->view;
        }
    }

    /**
     * sets the Rendering Engine.
     *
     * @param string $renderEngineName Name of the RenderEngine
     */
    public function setRenderEngine($renderEngineName)
    {
        $this->renderEngineName = $renderEngineName;

        $this->request->getRoute()->setRenderEngine($renderEngineName);
    }

    /**
     * Returns the Name of the Rendering Engine.
     * Returns Json if an XMLHttpRequest is given.
     * Returns Smarty as default if no rendering engine is set.
     *
     * @return string object, smarty as default
     */
    public function getRenderEngineName()
    {
        // check if the requesttype is xmlhttprequest (ajax) is incomming, then we will return data in json format
        if ($this->request->isAjax()) {
            $this->setRenderEngine('json');
        }

        // use smarty as default, if renderEngine is not set and it's not an ajax request
        if (empty($this->renderEngineName)) {
            $this->setRenderEngine('smarty');
        }

        return $this->renderEngineName;
    }

    /**
     * Returns the Rendering Engine Object via view_factory.
     *
     * @return renderengine object
     */
    public function getRenderEngine()
    {
        return \Koch\View\Factory::getRenderer($this->getRenderEngineName(), self::getInjector());
    }

    /**
     * Sets the Render Mode.
     *
     * @param string $mode The RenderModes are LAYOUT or PARTIAL.
     */
    public function setRenderMode($mode)
    {
        $this->getView()->renderMode = $mode;
    }

    /**
     * Get the Render Mode.
     *
     * @return string LAYOUT|PARTIAL
     */
    public function getRenderMode()
    {
        if (empty($this->getView()->renderMode)) {
            $this->getView()->renderMode = 'LAYOUT';
        }

        return $this->getView()->renderMode;
    }

    /**
     * modulecontroller->display();.
     *
     * All Output is done via the Response Object.
     * ModelData -> View -> Response Object
     *
     * 1. getTemplateName() - get the template to render.
     * 2. getView() - gets an instance of the render engine.
     * 3. assign model data to that view object (a,b,c)
     * 5. set data to response object
     *
     * @param $templates mixed|array|string Array with keys 'layout_template' / 'content_template' and templates
     * as values or just content template name.
     */
    public function display($templates = null)
    {
        // get the view
        $this->view = $this->getView();

        // get the view mapper
        $view_mapper = $this->view->getViewMapper();

        // set layout and content template by parameter array
        if (is_array($templates)) {
            if ($templates['layout_template'] !== null) {
                $view_mapper->setLayoutTemplate($templates['layout_template']);
            }

            if ($templates['content_template'] !== null) {
                $view_mapper->setTemplate($templates['content_template']);
            }
        }

        // only the "content template" was set
        if (is_string($templates)) {
            $view_mapper->setTemplate($templates);
        }

        // get templatename
        $template = $view_mapper->getTemplateName();

        // Debug display of Layout Template and Content Template
        //\Koch\Debug\Debug::firebug('Layout/Wrapper Template: ' . $this->view->getLayoutTemplate() . '<br />');
        //\Koch\Debug\Debug::firebug('Template Name: ' . $template . '<br />');

        // render the content / template
        $content = $this->view->render($template);

        // push content to the response object
        $this->response->setContent($content);

        unset($content, $template);
    }

    /**
     * This loads and initializes a formular from the module directory.
     *
     * @param string $formname       The name of the formular.
     * @param string $module         The name of the action.
     * @param bool   $assign_to_view If true, the form is directly assigned as formname to the view
     */
    public function loadForm($formname = null, $module = null, $action = null, $assign_to_view = true)
    {
        if (null === $module) {
            $module = $this->request->getRoute()->getModule();
        }

        if (null === $action) {
            $action = $this->request->getRoute()->getAction();
        }

        if (null === $formname) {
            // construct form name like "news"_"action_show"
            $formname = ucfirst((string) $module) . '_' . ucfirst((string) $action); // @todo adjust to PSR0
        }

        // construct formname, classname, filename, load file, instantiate the form
        $classname = 'Koch\Form\\' . $formname;
        $filename  = mb_strtolower($formname) . 'Form.php';
        $directory = APPLICATION_MODULES_PATH . mb_strtolower((string) $module) . '/Form/';

        Loader::requireFile($directory . $filename, $classname);

        // form preparation stage (combine description and add additional formelements)
        $form = new $classname();

        // assign form object directly to the view or return to work with it
        if ($assign_to_view === true) {
            // do not call $form->render(), it's already done
            $this->getView()->assign('form', $form);
        } else {
            return $form;
        }
    }

    /**
     * Redirect to Referer.
     */
    public function redirectToReferer()
    {
        $referer = $this->request->getReferer();

        // we have a referer in the environment
        if (empty($referer) === false) {
            $this->redirect(SERVER_URL . $referer);
        } else { // build referer on base of the current module
            $route = $this->request->getRoute();

            // we use internal rewrite style here: /module/action
            $redirect_to = '/' . $route->getModuleName();

            $submodule = $route->getSubModuleName();

            if (empty($submodule) === false) {
                $redirect_to .= '/' . $submodule;
            }

            // redirect() builds the url
            $this->response->redirect($redirect_to);
        }
    }

    /**
     * Shortcut for Redirect with an 404 Response Code.
     *
     * @param string $url  Redirect to this URL
     * @param int    $time seconds before redirecting (for the html tag "meta refresh")
     */
    public function redirect404($url, $time = 5)
    {
        $this->response->redirect($url, $time, 404, _('The URL you requested is not available.'));
    }

    /**
     * Redirects to a new URL.
     * It's a proxy method using the HttpResponse Object.
     *
     * @param string $url        Redirect to this URL.
     * @param int    $time       Seconds before redirecting (for the html tag "meta refresh")
     * @param int    $statusCode Http status code, default: '303' => 'See other'
     * @param string $message    Text of redirect message.
     * @param string $mode       The redirect mode: LOCATION, REFRESH, JS, HTML.
     */
    public function redirect($url, $time = 0, $statusCode = 303, $message = null, $mode = null)
    {
        $this->response->redirect($url, $time, $statusCode, $message, $mode);
    }

    /**
     * addEvent (shortcut for usage in modules).
     *
     * @param string Name of the Event
     * @param object Eventobject
     */
    public function addEvent($eventName, \Koch\Event\Event $event)
    {
        \Koch\Event\Dispatcher::instantiate()->addEventHandler($eventName, $event);
    }

    /**
     * triggerEvent is shortcut/convenience method for Eventdispatcher->triggerEvent.
     *
     * @param mixed (string|object) $event   Name of Event or Event object to trigger.
     * @param object                $context Context of the event triggering, often simply ($this). Default Null.
     * @param string                $info    Some pieces of information. Default Null.
     */
    public function triggerEvent($event, $context = null, $info = null)
    {
        \Koch\Event\Dispatcher::instantiate()->triggerEvent($event, $context = null, $info = null);
    }

    /**
     * Shortcut to set a Flashmessage.
     *
     * @param string $type    string error, warning, notice, success, debug
     * @param string $message string A textmessage.
     */
    public static function setFlashmessage($type, $message)
    {
        \Koch\Session\Flashmessages::setMessage($type, $message);
    }

    /**
     * Adds a new breadcrumb.
     *
     * @param string $title                  Name of the trail element. Use Gettext _('Title')!
     * @param string $link                   Link of the trail element
     * @param string $replace_array_position Position in the array to replace with name/trail. Start = 0.
     */
    public static function addBreadcrumb($title, $link = '', $replace_array_position = null)
    {
        \Koch\View\Helper\Breadcrumb::add($title, $link, $replace_array_position);
    }

    /**
     * Returns the HttpRequest Object.
     *
     * @return HttpRequest
     */
    public function getHttpRequest()
    {
        return $this->request;
    }

    /**
     * Returns the HttpResponse Object.
     *
     * @return \Koch\Http\HttpResponse
     */
    public function getHttpResponse()
    {
        return $this->response;
    }
}
