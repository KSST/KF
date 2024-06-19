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

use Koch\Cache\Cache;
use Koch\Http\HttpRequestInterface;

/**
 * Router.
 *
 * Router does URL Formatting and internal Rewriting.
 * The URL is segmented and restructured to fit the internal route to a controller.
 * The internal routes are described in a central routing configuration file.
 * This central config is updated on installation and deinstallation of modules and plugins.
 *
 * @see \Koch\Routes\Manager
 *
 * Normally all requests made map to a specific physical resource rather than a logical name.
 * With Routing you are able to map a logical name to a specific physical name.
 * Examples: map a logical URL (a mod_rewritten one) to a Controller/Method/Parameter
 * or map a FileRequest via logical URL (a mod_rewritten one) to a DownloadController/Method/Parameters.
 * Routes are a valuable concept because they separate your URLs from your data.
 *
 * There are two different URL Formatings allowed:
 * 1. Slashes as Segment Dividers-Style, like so: /mod/sub/action/id
 * 2. Fake HTML File Request or SMF-Style, like so: /mod.sub.action.id.html
 */
class Router implements RouterInterface, \ArrayAccess
{
    /**
     * @var object Koch\Config
     */
    private $config;

    /**
     * Whether to use caching for routes or not.
     *
     * @var bool
     */
    private static $useCache = false;

    /**
     * The Request URI (came in from the HttpRequest object).
     *
     * @var string
     */
    private $uri = '';

    /**
     * The Request URI as an array.
     *
     * @var array
     */
    public $uriSegments = [];

    /**
     * The "extension" on the URI
     * Would be "html" for the URI "/news/show/1.html".
     *
     * @var string
     */
    private static $extension = '';

    /**
     * Routes Mapping Table.
     * Is an array containing several route definitions.
     *
     * @var array Routes Array
     */
    private $routes = [];

    /**
     * Constructor.
     */
    public function __construct(HttpRequestInterface $request)
    {
        $this->request = $request;

        // get URI from request, clean it and set it as a class property
        $this->uri = self::prepareRequestURI($request->getRequestURI());
    }

    /**
     * Get and prepare the SERVER_URL/URI.
     *
     * Several fixes are applied to the $request_uri.
     *
     * When incomming via \Koch\Http\HttpRequest::getRequestURI()
     * the $request_rui is already
     * (1) lowercased and
     * (2) urldecoded.
     *
     * This function
     * (3) strips slashes from the beginning and the end,
     * (4) prepends a slash and
     * (5) strips PHP_SELF from the uri string.
     *
     * A multislash removal is not needed, because of the later usage of preg_split().
     *
     * @return string Request URL
     */
    public function prepareRequestURI($uri)
    {
        // remove xdebug_session_start parameter from uri
        if (function_exists('xdebug_break')) {
            $uri = str_replace('xdebug_session_start=netbeans-xdebug', '', $uri);
            // remove trailing '?' or '&'
            $uri = rtrim($uri, '?&');
        }

        // add slash in front + remove slash at the end
        if ($uri !== '/') {
            $uri = '/' . trim((string) $uri, '/');
        }

        $this->uri = $uri;

        return $uri;
    }

    /**
     * Adds a route.
     *
     * @param string $url_pattern  A route string.
     * @param array  $requirements Routing options.
     */
    public function addRoute($url_pattern, array $requirements = null)
    {
        /*
         * 1) Preprocess the route
         */

        $url_pattern = ltrim($url_pattern, '/');

        /*
         * Replace all static placeholders, like (:num) || (:id)
         * with their equivalent regular expression ([0-9]+).
         *
         * All static placeholders not having a regexp equivalent,
         * will remain on the route, like ":news".
         * They will be handled as "static named" routes and route directly to
         * a controller with the same name!
         */
        if (str_contains($url_pattern, '(')) {
            $url_pattern = self::placeholdersToRegexp($url_pattern);
        }

        // explode the uri pattern to get uri segments
        $segments = explode('/', (string) $url_pattern);

        // combines all regexp patterns of segements to one regexp pattern for the route
        $regexp = $this->processSegmentsRegExp($segments, $requirements);

        $options = [
            'regexp'             => $regexp,
            'number_of_segments' => count($segments),
            'requirements'       => $requirements,
        ];

        /*
         * 2) Finally add the now *preprocessed* Route.
         */
        $this->routes['/' . $url_pattern] = $options;
    }

    /**
     * Returns a regexp pattern for the route
     * by combining the regexp patterns of all uri segments.
     *
     * It's basically string concatenation of regexp strings.
     *
     * @param array $segments     Array with URI segments.
     * @param array $requirements Array with
     *
     * @return string Regular Expression for the route.
     */
    public function processSegmentsRegExp(array $segments, array $requirements = null)
    {
        // start regular expression
        $regexp = '#';

        // process all segments
        foreach ($segments as $segment) {

            /*
             * Process "Static Named Parameters".
             *
             * Static named parameters starts with a ":".
             * Example: ":contoller".
             */
            if (str_contains((string) $segment, ':')) {
                $name = substr((string) $segment, 1); // remove :

                // is there a requirement for this param? 'id' => '([0-9])'
                if (isset($requirements[$name])) {
                    // add it to the regex
                    $regexp .= '(?P<' . $name . '>' . $requirements[$name] . ')';
                    // and remove the now processed requirement
                    unset($requirements[$name]);
                } else { // no requirement
                    $regexp .= '(?P<' . $name . '>[a-z_-]+)';
                }
            } else {
                /*
                 * Process "Static Parameter".
                 *
                 * Static parameters starts with a "/".
                 * Example: "/index" or "/news".
                 */
                $regexp .= '\\/' . $segment;
            }

            // regexp between segments (regexp combiner)
            $regexp .= '\/?';
        }

        // finish regular expression
        $regexp .= '#';

        return $regexp;
    }

    /**
     * Add multiple route.
     *
     * @param array $routes Array with multiple routes.
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route => $options) {
            $this->addRoute((string) $route, (array) $options);
        }
    }

    /**
     * Method returns all loaded routes.
     *
     * @return array Returns array with all loaded Routes.
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Delete a route by its url pattern.
     *
     * @param string $url_pattern
     */
    public function delRoute($url_pattern)
    {
        unset($this->routes[$url_pattern]);
    }

    /**
     * Resets the routes array.
     *
     * @param bool Load the default routes. Defaults to false.
     *
     * @return Router \Koch\Router\Router
     */
    public function reset($loadDefaultRoutes = false)
    {
        $this->routes = [];

        TargetRoute::reset();

        if ($loadDefaultRoutes === true) {
            $this->loadDefaultRoutes();
        }

        return $this;
    }

    /**
     * Generates a URL by parameters.
     *
     * @param string $url_pattern The URL Pattern of the route
     * @param array  $params      An array of parameters
     * @param bool   $absolute    Whether to generate an absolute URL
     *
     * @return string The generated (relative or absolute) URL.
     */
    public function generateURL($url_pattern, array $params = null, $absolute = false)
    {
        // @todo

        /*$url = '';

        // @todo merge with buildURL + routing rules + parameters

        $url_pattern = $url_pattern;

        $params = $params;

        if ($absolute) {
        } else {
        }*/

        return $url;
    }

    /**
     * Builds a url string.
     *
     * @param $url Array or String to build the url from (e.g. '/news/admin/show')
     * @param $encode bool True (default) encodes the "&" in the url (amp).
     */
    public static function buildURL($url, $encode = true)
    {
        // if urlstring is array, then a relation (urlstring => parameter_order) is given
        if (is_array($url)) {
            $parameterOrder = '';
            $parameterOrder = current($url);
            $urlValue = current($url);
            $urlKey = key($url);
            $parameterOrderKey = key($url);
            next($url);
        }

        // return, if urlstring is already a qualified url (http://...)
        if (str_contains((string) $url, WWW_ROOT . 'index.php?')) {
            return $url;
        }

        // only the http prefix is missing
        if (str_contains((string) $url, 'index.php?')) {
            return 'http://' . $url;
        }

        // cleanup: remove all double slashes
        while (str_contains((string) $url, '//')) {
            $url = str_replace('//', '/', $url);
        }

        // cleanup: remove space and slashes from begin and end of string
        $url = trim((string) $url, ' /');

        /*
         * Mod_Rewrite is ON.
         *
         * The requested url style is:
         * ROOT/news/2
         */
        if (self::isRewriteEngineOn() === true) { /* self::checkEnvForModRewrite() */

            return WWW_ROOT . ltrim($url, '/');
        } else {
            /*
             * Mod_Rewrite is OFF.
             *
             * The requested url style is:
             * ROOT/index.php?mod=new&ctrl=admin&action=show&id=2
             */

            // get only the part after "index.php?"
            if (str_contains($url, 'index.php?')) {
                $url = strstr($url, 'index.php?');
            }

            // $urlstring contains something like "/news/show/2"
            // explode the string into an indexed array
            $urlParameters = explode('/', $url);

            // do we have a parameter_order given?
            if (isset($parameterOrder)) {
                // replace parameter names with shorthands used in the url
                $search         = ['module', 'controller', 'action'];
                $replace        = ['mod', 'ctrl', 'action'];
                $parameterOrder = str_replace($search, $replace, $parameterOrder);

                $urlKeys = explode('/', $parameterOrder);
            } else {
                // default static whitelist for url parameter keys
                $urlKeys = ['mod', 'ctrl', 'action', 'id', 'type'];
            }

            /*
             * This turns the indexed url parameters array into a named one.
             * [0]=> "news"  to  [mod]    => "news"
             * [1]=> "show"  to  [action] => "show"
             * [2]=> "2"     to  [id]     => "2"
             */
            $urlData = \Koch\Functions\Functions::arrayUnequalCombine($urlKeys, $urlParameters);

            // determine the separator. it defaults to "&amp;" for internal usage in html documents
            $argSeparator = ($encode === true) ? '&amp;' : '&';

            // Finally: build and return the url!
            return WWW_ROOT . 'index.php?' . http_build_query($urlData, '', $argSeparator);
        }
    }

    /**
     * Main method of \Koch\Router\Router.
     *
     * The routing workflow is
     * 1. firstly, check if ModRewrite is enabled,
     *    this decides upon which URL parser to use.
     * 2. URL parser splits the uri into uri segments.
     * 3. routes are initialized (the defaultRoute and all module routes)
     * 4. try to find a route/map matching with the uri_segments
     * 5. if no mapping applies, then set default values from config and fallback to a static routing
     * 6. always! -> found_route -> call!
     *
     * @return TargetRoute|null
     */
    public function route()
    {
        /*
         * If there are no uri segments, loading routes and matching is pointless.
         * Instead dispatch to the default route and return the according TargetRoute object.
         */
        if (empty($this->uri) or $this->uri === '/') {
            return $this->dispatchToDefaultRoute();
        }

        // initialize Routes
        $this->loadDefaultRoutes();

        /*
         * Now match the URI against the Routes.
         * The result is either a "dispatchable target route object" or "No target route found.".
         */
        $targetRoute = $this->match();

       /*
         * Inject the target route object back to the request.
         * Thereby the request gains full knowledge about the URL mapping (external to internal).
         * We might ask the request object later, where the requests maps to.
         */
        $this->request->setRoute($targetRoute);

        return $targetRoute;
    }

    public function dispatchToDefaultRoute()
    {
        $targetRoute = TargetRoute::instantiate();
        // was the default route configured correctly
        // @todo this is only possible if set from config to target route
        //if ($targetRoute::dispatchable()) {
        // default route is dispatchable, set it to the request
        $this->request->setRoute($targetRoute);

        return $targetRoute;
        // } else {
        // an undispatchable route was configured
        //    self::dispatchTo404();
        //}
    }

    public static function dispatchTo404()
    {
        TargetRoute::setController('error');
        TargetRoute::setAction('routenotfound');
    }

    /**
     * Renameds URL shorthands like "mod" to "module".
     * This is needed, because routing might be noRewrite.
     * So the uri segments array might contain something like "mod" => "news".
     * We need this to be "module" => "news" for setting it to the TargetRoute.
     *
     * @param $array
     *
     * @return $array
     */
    public static function fixNoRewriteShorthands($array)
    {
        if (isset($array['mod'])) {
            $array['module'] = $array['mod'];
            unset($array['mod']);
        }

        return $array;
    }

    /**
     * Setter Method for URI. Needed for testing.
     *
     * @param string $uri
     */
    public function setRequestURI($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Matches the URI against the Routes Mapping Table.
     * Taking static, dynamic and regexp routings into account.
     * In other words, it "map matches the URI".
     *
     * @return TargetRoute|null
     */
    public function match()
    {
        // do we have some routes now?
        if (0 === count($this->routes)) {
            throw new \OutOfBoundsException('The routes lookup table is empty. Define some routes.');
        }

        /*
         * Detects if Mod_Rewrite engine is active and
         * calls the proper URL Parser/Segmentizer method for the extraction of uri segments.
         */
        if (static::isRewriteEngineOn() or isset($_ENV['FORCE_MOD_REWRITE_ON']) and
                true === empty($_GET['mod']) and true === empty($_GET['ctrl'])) {
            $this->uriSegments = self::parseUrlRewrite($this->uri);
        } else {
            $this->uriSegments = $this->parseUrlNoRewrite($this->uri);

            $this->uriSegments = self::fixNoRewriteShorthands($this->uriSegments);

            $targetRoute = TargetRoute::setSegmentsToTargetRoute($this->uriSegments);

            if ($targetRoute::dispatchable()) {
                return $targetRoute;
            }
        }

        /*
         * Reduce the map lookup table, by dropping all routes
         * with more segments than the current requested uri.
         */
        if (count($this->routes) > 1 and count($this->uriSegments) >= 1) {
            self::reduceRoutesToSegmentCount();
        }

        /*
         * Process:     Static Route
         *
         * Do we have a direct match ?
         * This matches "static routes". Without any preg_match overhead.
         *
         * Example:
         * The request URI "/news/index" relates 1:1 to $routes['/news/index'].
         * The request URI "/login"      relates 1:1 to $routes['/login']
         */
        if (isset($this->routes[$this->uri])) {

            // we have a direct match
            $found_route = $this->routes[$this->uri];

            // return the TargetRoute object
            return TargetRoute::setSegmentsToTargetRoute($found_route);
        } else {

            /*
             * No, there wasn't a 1:1 match.
             * Now we have to check the uri segments.
             *
             * Let's loop over the remaining routes and try to map match the uri_segments.
             */
            foreach ($this->routes as $route_pattern => $route_values) {
                unset($route_pattern);

                $matches = '';

                /**
                 * Process:     Dynamic Regular Expression Parameters.
                 *
                 * Example:
                 * URI: /news
                 * Rule /:controller
                 * Regexp: "#(?P<controller>[a-z0-9_-]+)\/?#"
                 * Matches: $matches['controller'] = 'news';
                 */
                if (1 === preg_match($route_values['regexp'], $this->uri, $matches)) {

                    // matches[0] contains $this->uri
                    unset($matches[0]);

                    // remove duplicate values
                    // e.g. [controller] = news
                    //      [1]          = news
                    $matches = array_unique($matches);

                    # @todo # fetch key and its position from $route_values['requirements']
                    if (count($route_values['requirements']) > 0) {
                        foreach ($route_values['requirements'] as $array_position => $key_name) {

                            // insert a new key
                            // with name from requirements array
                            // and value from matches array
                            // ([id] => 42)
                            $pos                = $array_position + 1;
                            $matches[$key_name] = $matches[$pos];

                            // remove the old not-named key ([2] => 42)
                            unset($matches[$pos]);
                        }
                    }

                    // insert $matches[<controller>] etc
                    TargetRoute::setSegmentsToTargetRoute($matches);
                }

                if (TargetRoute::dispatchable()) {
                    // route found, stop foreach
                    break;
                } else {
                    TargetRoute::reset();
                    continue;
                }
            }
        }

        return TargetRoute::instantiate();
    }

    /**
     * Parses the URI and returns an array with URI segments.
     *
     * URL Parser for Apache Mod_Rewrite URL/URIs.
     * Think of it as a ModRewrite_Request_Resolver.
     *
     * This is based on htaccess rewriting with [QSA,L] (Query Append String).
     *
     * @param string $uri
     *
     * @return array Array with URI segments.
     */
    private static function parseUrlRewrite($uri)
    {
        $uri = str_replace(strtolower((string) $_SERVER['SCRIPT_NAME']), '', $uri);

        /*
         * The query string up to the question mark (?)
         *
         * Removes everything after a "?".
         * Note: with correct rewrite rules in htaccess, this conditon is not needed.
         */
        $pos = mb_strpos($uri, '?');
        if ($pos !== false) {
            $uri = mb_substr($uri, 0, $pos);
        }

        /*
         * The last dot (.)
         *
         * This detects the extension and removes it from the uri string.
         * The extension determines the output format. It is always the last piece of the URI.
         * Even if there are multiple points in the url string this processes only the last dot
         * and fetches everything after it as the extension.
         */
        $pos = mb_strpos($uri, '.');
        if ($pos !== false) {
            $uri_dot_array = [];
            // Segmentize the url into an array
            $uri_dot_array = explode('.', $uri);
            // chop off the last piece as the extension
            self::$extension = array_pop($uri_dot_array);
            // there might be multiple dots in the url
            // thats why implode is used to reassemble the segmentized array to a string again
            // but note the different glue string: the dots are now replaced by slashes ,)
            // = ini_get('arg_separator.output')
            $uri = implode('/', $uri_dot_array);
            unset($uri_dot_array);
        }
        unset($pos);

        /*
         * The slashes (/) and empty segments (double slashes)
         *
         * This segmentizes the URI by splitting at slashes.
         */
        $uri_segments = preg_split('#/#', $uri, -1, PREG_SPLIT_NO_EMPTY);
        unset($uri);

        /*
         * Finished!
         */

        return $uri_segments;
    }

    /**
     * Parses the URI and returns an array with URI segments.
     *
     * URL Parser for NoRewrite URL/URIs.
     * This URLParser has to extract mod, sub, action, id/parameters from the URI.
     * Alternate name: Standard_Request_Resolver.
     *
     * @param string $uri
     *
     * @return array Array with URI segments.
     */
    private function parseUrlNoRewrite($uri)
    {
        if (str_contains('?', $uri)) {
            return [0 => $uri];
        }

        // use some parse_url magic to get the url_query part from the uri
        $uri_query_string = parse_url($uri, PHP_URL_QUERY);
        unset($uri);

        /*
         * The ampersand (&)
         *
         * Use ampersand as the split char for string to array conversion.
         */
        $uri_query_array = explode('&', $uri_query_string);

        /*
         * The equals sign (=)
         *
         * This addresses the pair relationship between parameter name and value, like "id=77".
         */
        $uri_segments = [];

        if (count($uri_query_array) > 0) {
            $key        = '';
            $value      = '';
            $query_pair = '';
            foreach ($uri_query_array as $query_pair) {
                if (str_contains($query_pair, '=')) {
                    [$key, $value]  = explode('=', $query_pair);
                    $uri_segments[$key] = $value;
                }
            }
            unset($query_pair, $key, $value);
        }
        unset($uri_query_string, $uri_query_array);

        // Finished!
        return $uri_segments;
    }

    /**
     * Check if Apache "mod_rewrite" is activated in configuration.
     *
     * @return bool True, if "mod_rewrite" enabled. False otherwise.
     */
    public static function isRewriteEngineOn()
    {
        // via constant
        if (defined('REWRITE_ENGINE_ON') && REWRITE_ENGINE_ON === true) {
            return true;
        }

        // via config
        /*if (isset($this->config['router']['mod_rewrite'])) {
            $bool = (bool) $this->config['router']['mod_rewrite'];
            define('REWRITE_ENGINE_ON', $bool);

            return $bool;
        }*/

        return false; # $this->checkEnvForModRewrite();
    }

    /**
     * Checks if Apache Module "mod_rewrite" is loaded/enabled
     * and Rewrite Engine is enabled in .htaccess".
     *
     * @return bool True, if mod_rewrite on.
     */
    public static function checkEnvForModRewrite()
    {
        // ensure apache has module mod_rewrite active
        if (true === function_exists('apache_get_modules') and
            true === in_array('mod_rewrite', apache_get_modules(), true)) {
            if (true === is_file(APPLICATION_PATH . '.htaccess')) {
                // load htaccess and check if RewriteEngine is enabled
                $htaccess_content = file_get_contents(APPLICATION_PATH . '.htaccess');
                $rewriteEngineOn  = preg_match('/.*[^#][\t ]+RewriteEngine[\t ]+On/i', $htaccess_content);

                if (true === (bool) $rewriteEngineOn) {
                    return true;
                } else {
                    // @todo Hint: Please enable mod_rewrite in htaccess.
                    return false;
                }
            } else {
                // @todo Hint: No htaccess file found. Create and enable mod_rewrite.
                return false;
            }
        } else {
            // @todo Hint: Please enable mod_rewrite module for Apache.
            return false;
        }
    }

    /**
     * Replaces the placeholders in a route, like alpha, num, word
     * with their regular expressions for later preg_matching.
     * This is used while adding a new Route.
     *
     * @param string $route_with_placeholders A Route with a placeholder like alpha or num.
     */
    public static function placeholdersToRegexp($route_with_placeholders)
    {
        $placeholders = ['(:id)', '(:num)', '(:alpha)', '(:alphanum)', '(:any)', '(:word)',
                              '(:year)', '(:month)', '(:day)', ];

        $replacements = ['([0-9]+)', '([0-9]+)', '([a-zA-Z]+)', '([a-zA-Z0-9]+)', '(.*)', '(\w+)',
                              '([12][0-9]{3})', '(0[1-9]|1[012])', '(0[1-9]|1[012])', ];

        return str_replace($placeholders, $replacements, $route_with_placeholders);
    }

    /**
     * This unsets all Routes of Routing Table ($this->routes)
     * which have more segments then the request uri.
     */
    public function reduceRoutesToSegmentCount()
    {
        $route_pattern           = '';
        $route_values            = '';
        $number_of_uri_segements = count($this->uriSegments);

        foreach ($this->routes as $route_pattern => $route_values) {
            if ($route_values['number_of_segments'] === $number_of_uri_segements) {
                continue;
            } else {
                unset($this->routes[$route_pattern]);
            }
        }

        unset($route_pattern, $route_values);
    }

    /**
     * Register the default routes.
     */
    public function loadDefaultRoutes()
    {
        // Is Routes Caching is enabled in config?
        if (isset($this->config['router']['caching'])) {
            self::$useCache = ($this->config['router']['caching'] === true) ? true : false;
        }

        // Load Routes from Cache
        if (true === self::$useCache and true === empty($this->routes) and
            Cache::contains('clansuite.routes')) {
            $this->addRoutes(Cache::read('clansuite.routes'));
        }

        // Load Routes from Config "routes.config.php"
        if (true === empty($this->routes)) {
            $routes = Manager::loadRoutesFromConfig();
            if ($routes) {
                $this->addRoutes($routes);
            }

            // and save these routes to cache
            if (true === self::$useCache) {
                Cache::store('clansuite.routes', $this->getRoutes());
            }
        }

        /*
         * Connect some default fallback Routes
         *
         * Example for Route definition with ArrayAccess: $r['/:controller'];
         */
        if (empty($this->routes)) {
            # one segment
            //// "/news" (list)
            $this->addRoute('/:module');
            # two segments
            // "/news/new" (new)
            $this->addRoute('/:module/:action');
            // "/news/news" (list)
            $this->addRoute('/:module/:controller');
            // "/news/31" (show/update/delete)
            $this->addRoute('/:controller/(:id)', [1 => 'id']);
            // "/news/news/31" (show/update/delete)
            $this->addRoute('/:module/(:id)', [1 => 'id']);
            # three segments
            // "/news/news/new" (new)
            $this->addRoute('/:module/:controller/:action');
            // "/news/edit/42" (edit)
            $this->addRoute('/:controller/:action/(:id)', [2 => 'id']);
            // "/news/42/edit" (edit)
            $this->addRoute('/:module/(:id)/:action', [1 => 'id']);
            // "/news/news/31" (show/update/delete)
            $this->addRoute('/:module/:controller/(:id)', [2 => 'id']);
            # four segments
            // "/news/news/31/edit" (edit)
            $this->addRoute('/:module/:controller/(:id)/:action', [2 => 'id']);
            // "/news/news/edit/31" (edit)
            $this->addRoute('/:module/:controller/:action/(:id)', [3 => 'id']);
            # five segments
            // "/news/news/edit/31.html" (edit)
            $this->addRoute('/:module/:controller/:action/(:id)/:format', [4 => 'id']);
        }
    }

    /**
     * Implementation of SPL ArrayAccess.
     */

    /**
     * Instead of working with $router->addRoute(name,map);
     * you may now access the routing table as an array $router[$route] = $map;.
     */
    final public function offsetSet($route, $target)
    {
        $this->addRoute($route, $target);
    }

    final public function offsetGet($name)
    {
        if ((isset($this->routes[$name])) || (array_key_exists($name, $this->routes))) {
            return $this->routes[$name];
        } else {
            return;
        }
    }

    final public function offsetExists($name)
    {
        return isset($this->routes[$name]) === true || array_key_exists($name, $this->routes);
    }

    final public function offsetUnset($name)
    {
        unset($this->routes[$name]);
    }
}
