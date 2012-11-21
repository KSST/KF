<?php

namespace KochTest\Router;

use Koch\Router\Router;
use Koch\Router\TargetRoute;
use Koch\Mvc\Mapper;
use Koch\Http\HttpRequest;
use Koch\Config\Config;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '';

        $request = new HttpRequest();

        $config = new Config();

        $this->router = new Router($request, $config);

        // set the Fixtures folder with application and module classes for the autoloading
        set_include_path(realpath(__DIR__ . '/Fixtures') . PATH_SEPARATOR . get_include_path());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->router);
    }

    public function testMethod_addRoute()
    {
        $this->router->addRoute('/news/(:id)', array('controller', 'id'));

        $routes = $this->router->getRoutes();

        $this->assertEquals(2, $routes['/news/([0-9]+)']['number_of_segments']);
        $this->assertEquals('#\/news\/?\/([0-9]+)\/?#', $routes['/news/([0-9]+)']['regexp']);

        $this->router->reset();
    }

    public function testMethod_delRoute()
    {
        // static controller with dynamic id
        $this->router->addRoute('/news/(:id)', array(':controller', 'id'));

        $this->assertTrue(1 == count($this->router->getRoutes()));

        // @todo this is odd, because the string to delete the route
        // does not match the string, when adding the route,
        // due to preplacing with regexps
        $this->router->delRoute('/news/([0-9]+)');

        $this->assertTrue(0 == count($this->router->getRoutes()));
    }

    public function testMethod_reset()
    {
        $this->assertTrue(0 == count($this->router->getRoutes()));

        $this->router->addRoute('/news', array(':controller'));

        $this->assertTrue(1 == count($this->router->getRoutes()));

        $this->router->reset();

        $this->assertTrue(0 == count($this->router->getRoutes()));
    }

    public function testMethod_reset_resets_TargetRoute_too()
    {
        TargetRoute::setAction('testclass');
        $this->assertEquals('testclass', TargetRoute::getAction());
        $this->router->reset();

        // default module action is "list"
        // "list" was formerly "index" (Response Action for GET "/foos")
        $this->assertEquals('list', TargetRoute::getAction());
    }

    public function testMethod_addRoutes()
    {
        $routes = array(
            '/news'                   => array(':controller'),
            '/news/edit'              => array(':controller', ':action', 'id'),
            '/news/edit/(:id)'        => array(':controller', ':action', 'id'),
            '/news/admin/edit/(:id)'  => array(':controller', ':subcontroller', ':action', 'id'),
            '/news/:year/:month'      => array(':controller', ':year', ':month'),
            '/news/he-ad-li-ne-SEO'   => array(':controller', ':word')
        );

        $this->router->addRoutes($routes);

        $this->assertTrue( count($routes) === count($this->router->getRoutes()));
    }

    public function testMethod_addRoutes_via_ArrayAccess()
    {
        $r = $this->router;

        $r['/news']                  = array(':controller');
        $r['/news/edit']             = array(':controller', ':action', 'id');
        $r['/news/edit/(:id)']       = array(':controller', ':action', 'id');
        $r['/news/admin/edit/(:id)'] = array(':controller', ':subcontroller', ':action', 'id');
        $r['/news/:year/:month']     = array(':controller', ':year', ':month');
        $r['/news/he-ad-li-ne-text'] = array(':controller', ':word');

        $this->assertTrue( 6 === count($this->router->getRoutes()));
    }

    public function testMethod_removeRoutesBySegmentCount()
    {
        // adding 3 routes, each with different segment number
        $this->router->addRoute('/news', array(':controller'));
        $this->router->addRoute('/news/edit', array(':controller', ':action', 'id'));
        $this->router->addRoute('/news/edit/(:id)', array(':controller', ':action', 'id'));

        $this->assertTrue(3 == count($this->router->getRoutes()));

        // add only one segment
        $this->router->uriSegments = array('0' => 'news');

        // this makes all other routes irrelevant for the lookup
        $this->router->reduceRoutesToSegmentCount();

        $this->assertTrue(1 == count($this->router->getRoutes()));
    }

    public function testMethod_prepareRequestURI()
    {
        // prepends slash
        $this->assertEquals('/news', $this->router->prepareRequestURI('news'));

        // prepends slash and removes any trailing slashes
        $this->assertEquals('/news', $this->router->prepareRequestURI('news///'));

        // prepends slash
        $this->assertEquals('/news/edit', $this->router->prepareRequestURI('news/edit'));
    }

    public function testMethod_placeholdersToRegexp()
    {
        $this->assertEquals('/route/with/([0-9]+)', $this->router->placeholdersToRegexp('/route/with/(:id)'));
        $this->assertEquals('/route/with/([0-9]+)', $this->router->placeholdersToRegexp('/route/with/(:num)'));
        $this->assertEquals('/route/with/([a-zA-Z]+)', $this->router->placeholdersToRegexp('/route/with/(:alpha)'));
        $this->assertEquals('/route/with/([a-zA-Z0-9]+)', $this->router->placeholdersToRegexp('/route/with/(:alphanum)'));
        $this->assertEquals('/route/with/(.*)', $this->router->placeholdersToRegexp('/route/with/(:any)'));
        $this->assertEquals('/route/with/(\w+)', $this->router->placeholdersToRegexp('/route/with/(:word)'));
        $this->assertEquals('/route/with/([12][0-9]{3})', $this->router->placeholdersToRegexp('/route/with/(:year)'));
        $this->assertEquals('/route/with/(0[1-9]|1[012])', $this->router->placeholdersToRegexp('/route/with/(:month)'));
        $this->assertEquals('/route/with/(0[1-9]|1[012])', $this->router->placeholdersToRegexp('/route/with/(:day)'));
    }

    public function testMethod_processSegmentsRegExp()
    {
        $segments = array('news', 'edit', '([0-9]+)');
        $requirements = array('controller', 'action', ':num',);

        $this->assertSame(
            '#\/news\/?\/edit\/?\/([0-9]+)\/?#',
            $this->router->processSegmentsRegExp($segments, $requirements)
        );

        /**
         * Static Named Route
         */
        $segments = array(':news');
        $requirements = array('controller');

        $this->assertSame(
            '#(?P<news>[a-z_-]+)\/?#',
            $this->router->processSegmentsRegExp($segments, $requirements)
        );
    }

    public function testMethod_match_RestRoutes()
    {
        $applicationNamespace = 'KochTest\Router\Fixtures\Application';
        Mapper::setApplicationNamespace($applicationNamespace);
        define('MOD_REWRITE_ON', true);
        $_ENV['FORCE_MOD_REWRITE_ON'] = true;

        $this->router->reset(true);

        // http://example.com/news
        // routes to
        // Controller: News
        // Action: actionList()
        // Type: GET [REST Route]

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('/news');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',       $route->getModule());
        $this->assertEquals('News',       $route->getController());
        $this->assertEquals('list',       $route->getAction());
        $this->assertEquals('actionList', $route->getMethod());
        $this->assertEquals(array(),      $route->getParameters());
        $this->assertEquals('GET',        $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news/42
        // routes to
        // Controller: News
        // Action: actionShow()
        // Id: 42
        // Type: GET [REST Route]

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('/news/42');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',              $route->getModule());
        $this->assertEquals('News',              $route->getController());
        $this->assertEquals('actionShow',        $route->getMethod());
        $this->assertEquals(array('id' => '42'), $route->getParameters());
        $this->assertEquals('GET',               $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news/new
        // routes to
        // Controller: News
        // Action: actionNew()
        // Type: GET [REST Route]

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('/news/new');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',      $route->getModule());
        $this->assertEquals('News',      $route->getController());
        $this->assertEquals('actionNew', $route->getMethod());
        $this->assertEquals('GET',       $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news/42/edit
        // routes to
        // Controller: News
        // Action: actionEdit()
        // Id: 42
        // Type: GET [REST Route]

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('/news/42/edit');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',            $route->getModule());
        $this->assertEquals('News',            $route->getController());
        $this->assertEquals('actionEdit',      $route->getMethod());
        $this->assertSame(array('id' => '42'), $route->getParameters());
        $this->assertEquals('GET',             $route->getRequestMethod());
        $this->router->reset(true);

        // same as above with reversed last segements
        // http://example.com/news/edit/42
        // routes to
        // Controller: News
        // Action: actionEdit()
        // Id: 42
        // Type: GET [WEB]

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('/news/edit/42');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',              $route->getModule());
        $this->assertEquals('News',              $route->getController());
        $this->assertEquals('actionEdit',        $route->getMethod());
        $this->assertEquals(array('id' => '42'), $route->getParameters());
        $this->assertEquals('GET',               $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news/42
        // routes to
        // Controller: News
        // Action: actionUpdate()
        // Id: 42
        // Type: PUT [REST Route]
        // Post_parameters are filled.

        HttpRequest::setRequestMethod('PUT');
        $this->router->prepareRequestURI('/news/42');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',              $route->getModule());
        $this->assertEquals('News',              $route->getController());
        $this->assertEquals('actionUpdate',      $route->getMethod());
        $this->assertEquals(array('id' => '42'), $route->getParameters());
        $this->assertEquals('PUT',               $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news
        // routes to
        // Controller: News
        // Action: actionInsert()
        // Type: POST [REST Route]
        // Post_parameters are filled.

        // fake incoming env data
        $_POST['id'] = '42';
        $_POST['article_text'] = 'blabla';
        HttpRequest::setRequestMethod('POST');
        $this->router->prepareRequestURI('/news');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',         $route->getModule());
        $this->assertEquals('News',         $route->getController());
        $this->assertEquals('actionInsert', $route->getMethod());
        $this->assertEquals(array('id' => '42', 'article_text' => 'blabla'), $route->getParameters());
        $this->assertEquals('POST',         $route->getRequestMethod());
        $this->router->reset(true);

        // http://example.com/news/42
        // routes to
        // Controller: News
        // Action: actionDelete()
        // Id: 42
        // Type: DELETE [REST Route]

        HttpRequest::setRequestMethod('DELETE');
        $this->router->prepareRequestURI('/news/42');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',              $route->getModule());
        $this->assertEquals('News',              $route->getController());
        $this->assertEquals('actionDelete',      $route->getMethod());
        $this->assertEquals(array('id' => '42'), $route->getParameters());
        $this->assertEquals('DELETE',            $route->getRequestMethod());
        $this->router->reset(true);

        // same as above, web route
        // http://example.com/news/delete/42
        // routes to
        // Controller: News
        // Action: actionDelete()
        // Id: 42
        // Type: DELETE [WEB]

        HttpRequest::setRequestMethod('DELETE');
        $this->router->prepareRequestURI('/news/delete/42');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\News\Controller\NewsController',
            $route->getClassname()
        );
        $this->assertEquals('News',              $route->getModule());
        $this->assertEquals('News',              $route->getController());
        $this->assertEquals('actionDelete',      $route->getMethod());
        $this->assertEquals(array('id' => '42'), $route->getParameters());
        $this->assertEquals('DELETE',            $route->getRequestMethod());
        $this->router->reset(true);
    }

     public function testMethod_match_StaticRoute()
    {
        $applicationNamespace = 'KochTest\Router\Fixtures\Application';
        Mapper::setApplicationNamespace($applicationNamespace);

        // http://example.com/login

        $this->router->addRoute('/:controller');
        $this->router->addRoute('/:controller/:action');
        $this->router->addRoute('/:controller/:action/(:id)');

        $r = $this->router;
        $r['/login'] = array('module' => 'user', 'controller' => 'account', 'action' => 'login');

        $this->router->setRequestURI('/login');
        HttpRequest::setRequestMethod('GET');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\User\Controller\AccountController',
            $route->getClassname()
        );
        $this->assertEquals('User',        $route->getModule());
        $this->assertEquals('Account',     $route->getController());
        $this->assertEquals('actionLogin', $route->getMethod());
        $this->assertEquals(array(),       $route->getParameters());
        $this->assertEquals('GET',         $route->getRequestMethod());

        unset($route);
        $r->reset(true);

        // http://example.com/about

        $r = $this->router;
        $r['/about'] = array('module' => 'index', 'controller' => 'index', 'action' => 'about');

        $r->setRequestURI('/about');
        HttpRequest::setRequestMethod('GET');
        $route = $this->router->route();

        $this->assertEquals(
            $applicationNamespace . '\Modules\Index\Controller\IndexController',
            $route->getClassname()
        );
        $this->assertEquals('Index',       $route->getModule());
        $this->assertEquals('Index',       $route->getController());
        $this->assertEquals('actionAbout', $route->getMethod());
        $this->assertEquals(array(),       $route->getParameters());
        $this->assertEquals('GET',         $route->getRequestMethod());

        unset($route);
        $r->reset(true);
    }

    /*public function testMethod_match_CustomActionNames()
    {
        /*
         * Feature Idea:
        $this->router->route('controller_item', '/:controller/<:id>.:format',
            array('defaults' => array(
                'action' => 'view',
                'format' => 'html'),
                'get' => array('action' => 'show'),
                'put' => array('action' => 'update'),
                'delete' => array('action' => 'delete')
            )
        );
         */
    //}

    /* Feature not implemented yet.
    public function testMethod_match_SEO_Dynamic_Routes()
    {
        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('http://example.com/category/movies/Se7en.htm');
        $route = $this->router->route();

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('http://example.com/feeds/news/atom.xml');
        $route = $this->router->route();

        HttpRequest::setRequestMethod('GET');
        $this->router->prepareRequestURI('http://example.com/news/atom.xml');
        $route = $this->router->route();

        $this->markTestIncomplete('Test not implemented yet.');
    }*/

    /**
     * @expectedException OutOfBoundsException
     */
    public function testMethod_match_throwsExceptionIfNoRoutesFound()
    {
        $this->router->reset();

        $this->assertTrue(0 == count($this->router->getRoutes()));

        $this->assertTrue($this->router->match());
    }

    public function testMethod_generateURL()
    {
        /*
        $url = $this->router->generateURL($url_pattern);
        $this->assertEquals('url', $url);
         */
        $this->markTestIncomplete('Test not implemented yet.');

    }

    public function testMethod_buildURL_ModRewrite_OFF()
    {
        /**
         * Do not build an URL, if FQDN is passed and mod_rewrite is off.
         * like http://clansuite-dev.com/tests/index.php?mod=news&action=show
         * Just return the URL (pass-through).
         */
        $urlstring = WWW_ROOT . 'index.php?mod=news&action=show';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, false);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news&action=show', $url);

        $urlstring = WWW_ROOT . 'index.php?mod=news&action=show';
        $internal_url = true;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news&action=show', $url);

        /**
         * Build FQDN URL from internal slashed URLs, like
         * /news
         * /news/show
         * /news/admin/show/2
         *
         * So internally we use the mod_rewrite style.
         */
        /**
         * Parameter 1 - module
         */
        // removes crappy slashes - test 1
        $urlstring = '////news///';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news', $url);

        // removes crappy slashes - test 2
        $urlstring = '/news///';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news', $url);

        // route to module
        $urlstring = '/news';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news', $url);

        /**
         * Parameter 2 - action or controller
         */
        // route to module/action
        $urlstring = array('/news/show' => 'module/action');
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news&action=show', $url);

        // route to module/action/id
        $urlstring = array('/news/show/42' => 'module/action/id');
        $internal_url = true;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news&amp;action=show&amp;id=42', $url);

        // STANDARD PARAMETER ROUTING when MODREWRITE is OFF
        // we are not leaving any parameter out, so we don't need an urlstring description array
        // route to module/controller/action/id
        $urlstring = '/news/admin/edit/1';
        $internal_url = true;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'index.php?mod=news&amp;ctrl=admin&amp;action=edit&amp;id=1', $url);
    }

    public function testMethod_buildURL_ModRewrite_ON()
    {
        // precondition
        if(defined('REWRITE_ENGINE_ON') and REWRITE_ENGINE_ON == false) {
            $this->markTestSkipped('The Test depends on MOD_REWRITE.');
        } else {
             $this->assertTrue(REWRITE_ENGINE_ON);
        }

        /**
         * Build URL from internal slashed URLs, like
         * /news
         * /news/show
         * /news/admin/show/2
         *
         * So internally we use the mod_rewrite style.
         */
        /**
         * Parameter 1 - module
         */
        // removes crappy slashes - test 1
        $urlstring = '////news///';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news', $url);

        // removes crappy slashes - test 2
        $urlstring = '/news///';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news', $url);

        // removes crappy slashes - test 3
        $urlstring = '/////news';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news', $url);

        /**
         * Parameter 2 - action or sub
         */
        $urlstring = '/news/show';
        $internal_url = false;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news/show', $url);

        /**
         * Internal URLs (mod_rewrite style)
         * This should by-pass...
         */
        $urlstring = '/news/show/42';
        $internal_url = true;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news/show/42', $url);

        $urlstring = '/news/admin/edit/1';
        $internal_url = true;
        $url = $this->router->buildURL($urlstring, $internal_url);
        $this->assertEquals(WWW_ROOT . 'news/admin/edit/1', $url);
    }
}
