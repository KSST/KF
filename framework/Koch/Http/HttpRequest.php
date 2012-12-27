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

namespace Koch\Http;

/**
 * Class for Request Handling.
 *
 * It encapsulates the access to sanitized superglobals ($_GET, $_POST, $_SERVER).
 * There are two ways of access (1) via methods and (2) via spl arrayaccess array handling.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  HttpRequest
 */
class HttpRequest implements HttpRequestInterface, \ArrayAccess
{
    /**
     * @var array Contains the cleaned $_POST Parameters.
     */
    private $post_parameters;

    /**
     * @var array Contains the cleaned $_GET Parameters.
     */
    private $get_parameters;

    /**
     * @var array Contains the cleaned $_COOKIE Parameters.
     */
    private $cookie_parameters;

    /**
     * @var The requestmethod. Possible values are GET, POST, PUT, DELETE.
     */
    protected static $request_method;

    /**
     * @var string the base URL (protocol://server:port)
     */
    protected static $baseURL;

    /**
     * @var object Object with pieces of informations about the target route.
     */
    private static $route;

    /**
     * Construct the Request Object
     *
     * 1) Drop Superglobal $_REQUEST. Just hardcoded reminder for developers to not use it!
     * 2) Intrusion Detection System
     * 3) Additional Security Checks
     * 4) Clear Array, Filter and Assign the $_REQUEST Global to it
     * 5) Detect REST Tunneling through POST and set request_method accordingly
     */
    public function __construct() /*$ids_on = false*/
    {
        // 1) Drop $_REQUEST and $GLOBALS. Usage is forbidden!
        unset($_REQUEST);
        //unset($GLOBALS);

        /*if ($ids_on === true) {
            // 2) Run Intrusion Detection System (on GET, POST, COOKIES)
            $doorKeeper = new Koch\Security\DoorKeeper;
            $doorKeeper->runIDS();
        }*/

        /**
         *  3) Additional Security Checks
         */

        // block XSS
        $_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
        if (isset($_SERVER['QUERY_STRING'])) {
            htmlspecialchars($_SERVER['QUERY_STRING']);
        }

        /**
         *  5) Init Parameter Arrays and Assign the GLOBALS
         */

        // Clear Parameters Array
        $this->get_parameters       = array();
        $this->post_parameters      = array();
        $this->cookie_parameters    = array();

        // Assign the GLOBALS $_GET, $_POST, $_COOKIE
        $this->get_parameters     = $_GET;
        $this->post_parameters    = $_POST;
        $this->cookie_parameters  = $_COOKIE;

        /**
         * 6) Detect REST Tunneling through POST and set request_method accordingly
         */
        $this->detectRESTTunneling();
    }

    /**
     * Returns the raw POST Parameters Array.
     * Raw means: no validation, no filtering, no sanitization.
     *
     * @param  string $parameter Name of the Parameter
     * @return array  POST Parameters Array.
     */
    public function getPost($parameter = null)
    {
        if ($parameter === null) {
            return $this->post_parameters;
        }

        return $this->getParameter($parameter, 'POST');
    }

    /**
     * Returns the raw GET Parameters Array.
     * Raw means: no validation, no filtering, no sanitization.
     *
     * @param  string $parameter Name of the Parameter
     * @return array  GET Parameters Array.
     */
    public function getGet($parameter = null)
    {
        if ($parameter === null) {
            return $this->get_parameters;
        }

        return $this->getParameter($parameter, 'GET');
    }

    /**
     * Returns the COOKIES Parameters Array.
     * Raw means: no validation, no filtering, no sanitization.
     *
     * @param  string $parameter Name of the Parameter
     * @return array  COOKIES Parameters Array.
     */
    public function getCookie($parameter = null)
    {
        if ($parameter === null) {
            return $this->cookie_parameters;
        }

        return $this->getParameter($parameter, 'COOKIE');
    }

    /**
     * Shortcut to get a Parameter from $_SERVER
     *
     * @param  string $parameter Name of the Parameter
     * @return mixed  data | null
     */
    public function getServer($parameter)
    {
        if (in_array($parameter, array_keys($_SERVER))) {
            return $_SERVER[$parameter];
        } else {
            return null;
        }
    }

    /**
     * Returns the HTTP POST data in raw format via Stream.
     *
     * @return string HTTP POST data (raw).
     */
    public function getPostRaw()
    {
        $src = (php_sapi_name() === 'cli') ? 'php://stdin' : 'php://input';

        return file_get_contents($src);
    }

    /**
     * expectParameters
     *
     * a) isset test          -  to determine if the parameter is incomming
     * b) exception throwing  -  if parameter is not incomming, but expected
     * @todo c) validation          -  validates the incomming parameter via rules
     *
     * $parameters array structure:
     * $parameters = array(
     *  'parametername' => array (      // parametername as key for rules array
     *      'source',                   // (GET|POST)
     *      'validation-rule'
     * );
     * 'modulename' => array ('GET', 'string|lowercase')
     *
     * @example
     * // parameter names only
     * $this->expectParameters(array('modulename','language'));
     * // parameters, one with rules
     * // parameters, all with rules
     *
     * @param array $parameters
     */
    public function expectParameters(array $parameters)
    {
        foreach ($parameters as $parameter => $array_or_parametername) {
            /**
             * check if we have some rules to process
             */
            if (true === is_array($array_or_parametername)) {
                $array_name = $array_or_parametername[0];      // GET|POST|COOKIE
                #$validation_rules   = $array_or_parametername[1];      // some validation commands

                /**
                 * ISSET or Exception
                 */
                $this->expectParameter($parameter, $array_name);

                /**
                 * VALID or Exception
                 */
                #$this->validateParameter($parameter, $validation_rules);
            } else { // if(is_int($array_or_parametername))
                $this->expectParameter($array_or_parametername);
            }
        }
    }

    /**
     * This method ensures that all the parameters you are expecting
     * and which are required by your action are really incomming with the request.
     * It's a multiple call to issetParameter(), with the difference,
     * that it throws an Exception if not isset!
     *
     * a) isset test          -  to determine if the parameter is incomming
     * b) exception throwing  -  if parameter is not incomming, but expected
     *
     * @param string $parameter
     * @param string $array     (GET|POST|COOKIE)
     */
    public function expectParameter($parameter, $array = '')
    {
        // when array is not defined issetParameter will searches (POST|GET|COOKIE)
        if (is_string($array) === true) {
            if (false === $this->issetParameter($parameter)) {
                throw new \Koch\Exception\Exception('Incoming Parameter missing: "' . $parameter . '".');
            }
        } else { // when array is defined issetParameter will search the given array
            if (false === $this->issetParameter($parameter, $array)) {
                throw new \Koch\Exception\Exception(
                    'Incoming Parameter missing: "' . $parameter . '" in Array "' . $array . '".'
                );
            }
        }
    }

    /**
     * isset, checks if a certain parameter exists in the parameters array
     *
     * @param  string  $parameter Name of the Parameter
     * @param  string  $array     GET, POST, COOKIE. Default = GET.
     * @param  boolean $where     If set to true, method will return the name of the array the parameter was found in.
     * @return mixed   boolean string arrayname
     *
     */
    public function issetParameter($parameter, $array = 'GET', $where = false)
    {
        $array = mb_strtoupper($array);

        switch ($array) {
            case 'GET':
                if (isset($this->get_parameters[$parameter])) {
                    return ($where === false) ? true : 'get';
                }
                break;
            case 'POST':
                if (isset($this->post_parameters[$parameter])) {
                    return ($where === false) ? true : 'post';
                }
                break;
            case 'COOKIE':
                if (isset($this->cookie_parameters[$parameter])) {
                    return ($where === false) ? true : 'cookie';
                }
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * get, returns a certain parameter if existing
     *
     * @param string $parameter Name of the Parameter
     * @param string $array     GET, POST, COOKIE. Default = POST.
     * @param string $default   You can set a default value. It's returned if parametername was not found.
     *
     * @return mixed data | null
     */
    public function getParameter($parameter, $array = 'POST', $default = null)
    {
        /**
         * check if the parameter exists in $array
         * the third property of issetParameter is set to true, so that we get the full and correct array name back
         */
        $parameter_array = $this->issetParameter($parameter, $array, true);

        /**
         * we use type hinting here to cast the string with array name to boolean
         */
        if ((bool) $parameter_array === true) {
            // this returns a value from the parameterarray
            return $this->{mb_strtolower($parameter_array).'_parameters'}[$parameter];
        } elseif ($default !== null) {
            // this returns the default value,incomming via method property $default
            return $default;
        } else {
            return null;
        }
    }

    /**
     * set, returns a certain parameter if existing
     *
     * @param  string $parameter Name of the Parameter
     * @param  string $array     G, P, C. Default = POST.
     * @return mixed  data | null
     */
    public function setParameter($parameter, $array = 'POST')
    {
        if (true == $this->issetParameter($parameter, $array)) {
            return $this->{mb_strtolower($array).'_parameters'}[$parameter];
        } else {
            return null;
        }
    }

    /**
     * Get Value of a specific http-header
     *
     * @param  string $parameter Name of the Parameter
     * @return string
     */
    public static function getHeader($parameter)
    {
        $parameter = 'HTTP_' . mb_strtoupper(str_replace('-', '_', $parameter));

        if ($_SERVER[$parameter] !== null) {
            return $_SERVER[$parameter];
        }

        return null;
    }

    /**
     * Determine Type of Protocol for Webpaths (http/https)
     * Get for $_SERVER['HTTPS']
     *
     * @todo check $_SERVER['SSL_PROTOCOL'] + $_SERVER['HTTP_X_FORWARD_PROTO']?
     * @todo check -> or $_SERVER['SSL_PROTOCOL']
     *
     * @return string
     */
    public static function getServerProtocol()
    {
        if (self::isSecure()) {
             return 'https://';
        } else {
             return 'http://';
        }
    }

    /**
     * Determine Type of Protocol for Webpaths (http/https)
     * Get for $_SERVER['HTTPS'] with boolean return value
     *
     * @todo check about $_SERVER['SERVER_PORT'] == 443, is this always ssl then?
     * @see $this->getServerProtocol()
     * @return bool
     */
    public static function isSecure()
    {
        if (isset($_SERVER['HTTPS']) and (mb_strtolower($_SERVER['HTTPS']) === 'on' or $_SERVER['HTTPS'] == '1') ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine Port Number for Webpaths (http/https)
     * Get for $_SERVER['SERVER_PORT'] and $_SERVER['SSL_PROTOCOL']
     * @return string
     */
    private static function getServerPort()
    {
        // custom port
        if (self::isSecure() === false and $_SERVER['SERVER_PORT'] != 80) {
            return ':' . $_SERVER['SERVER_PORT'];
        }

        // custom ssl port
        if (self::isSecure() === true and $_SERVER['SERVER_PORT'] != 443) {
            return ':' . $_SERVER['SERVER_PORT'];
        }
    }

    /**
     * Returns the base of the current URL
     * Format: protocol://server:port
     *
     * The "template constant"" WWW_ROOT is later defined as getBaseURL
     * <form action="<?=WWW_ROOT?>/news/7" method="DELETE"/>
     *
     * @return string
     */
    public static function getBaseURL()
    {
        if ( empty(self::$baseURL) ) {
            // 1. Determine Protocol
            self::$baseURL = self::getServerProtocol();

            // 2. Determine Servername
            self::$baseURL .= self::getServerName();

            // 3. Determine Port
            self::$baseURL .= self::getServerPort();
        }

        return self::$baseURL;
    }

    /**
     * Get $_SERVER SERVER_NAME
     *
     * @return string The name of the server host under which the current script is executing.
     */
    public static function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get $_SERVER REQUEST_URI
     *
     * @return string The URI which was given in order to access this page; for instance, '/index.html'.
     */
    public static function getRequestURI()
    {
        if ($_SERVER['REQUEST_URI'] !== null) {
            return urldecode(mb_strtolower($_SERVER['REQUEST_URI']));
        }

        // MS-IIS and ISAPI Rewrite Filter (only on windows platforms)
        if (isset($_SERVER['HTTP_X_REWRITE_URL']) and stripos(PHP_OS, 'WIN') !== false) {
            return urldecode(mb_strtolower($_SERVER['HTTP_X_REWRITE_URL']));
        }

        $p = $_SERVER['SCRIPT_NAME'];
        if ($_SERVER['QUERY_STRING'] !== null) {
            $p .= '?' . $_SERVER['QUERY_STRING'];
        }

        return urldecode(mb_strtolower($p));
    }

    /**
     * Get $_SERVER REMOTE_URI
     *
     * @return string
     */
    public static function getRemoteURI()
    {
        return $_SERVER['REMOTE_URI'];
    }

    /**
     * Get $_SERVER QUERY_STRING
     *
     * @return string The query string via which the page was accessed.
     */
    public static function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * Get the current Url
     *
     * @return string Returns the current URL, which is the HOST + REQUEST_URI, without index.php.
     */
    public static function getCurrentUrl()
    {
        return str_replace('/index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * Get IP = $_SERVER REMOTE_ADDRESS
     *
     * @return string The IP/HOST from which the user is viewing the current page.
     */
    public static function getRemoteAddress()
    {
        $ip = null;

        if ($_SERVER['HTTP_CLIENT_IP'] !== null) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] !== null) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = array_pop($ip);
        }
        // NGINX - with natural russian config passes the IP as REAL_IP
        elseif ($_SERVER['HTTP_X_REAL_IP'] !== null) {
            $ip =  $_SERVER['HTTP_X_REAL_IP'];
        } elseif ($_SERVER['HTTP_FORWARDED_FOR'] !== null) {
            $ip =  $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_CLIENT_IP'] !== null) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] !== null) {
            $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_FORWARDED'] !== null) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED']) ) {
            $ip =  $_SERVER['HTTP_X_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (true === self::validateIP($ip)) {
            return $ip;
        }
    }

    /**
     * Returns the User Agent ($_SERVER HTTP_USER_AGENT)
     *
     * @return string String denoting the user agent being which is accessing the page.
     */
    public static function getUserAgent()
    {
        $ua = strip_tags($_SERVER['HTTP_USER_AGENT']);
        $ua_filtered = filter_var($ua, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        return $ua_filtered;
    }

    /**
     * Returns the Referrer ($_SERVER HTTP_REFERER)
     *
     * @return string The address of the page (if any) which referred the user agent to the current page.
     */
    public static function getReferer()
    {
        if ($_SERVER['HTTP_REFERER'] !== null) {
            $refr = strip_tags($_SERVER['HTTP_REFERER']);
            $refr_filtered = filter_var($refr, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        }

        return $refr_filtered;
    }

    /**
     * Validates a given IP
     *
     * @see getRemoteAddress()
     * @param  string  $ip   The IP address to validate.
     * @param  boolen  $ipv6 Boolean true, activates ipv6 checking.
     * @return boolean True, if IP is valid. False, otherwise.
     */
    public static function validateIP($ip, $ipv6 = false)
    {
        if (true === $ipv6) {
            return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        } else {
            return (bool) filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_IPV4
            );
        }
    }

    /**
     * Get Route returns the static Koch_TargetRoute object.
     *
     * With php onbord tools you can't debug this.
     * Please use \Koch\Debug\Debug:firebug($route); to debug.
     * Firebug uses Reflection to show the static properties and values.
     *
     * @return Koch_TargetRoute
     */
    public static function getRoute()
    {
       return (self::$route == null) ? \Koch\Router\TargetRoute::instantiate() : self::$route;
    }

    /**
     * Set Route
     *
     * @param $route The route container.
     */
    public static function setRoute($route)
    {
        self::$route = $route;
    }

    /**
     * REST Tunneling Detection
     *
     * This method takes care for REST (Representational State Transfer)
     * by tunneling PUT, DELETE through POST (principal of least power).
     * Ok, this is faked or spoofed REST, but lowers the power of POST
     * and it's short and nice in html forms.
     * @todo consider allowing 'GET' through POST?
     *
     * @see https://wiki.nbic.nl/index.php/REST.inc
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     */
    public function detectRESTTunneling()
    {
        $allowed_rest_methodnames = array('DELETE', 'PUT');

        // request_method has to be POST AND GET has to to have the method GET
        if (isset($_SERVER['REQUEST_METHOD']) === true and $_SERVER['REQUEST_METHOD'] == 'POST'
            and $this->issetParameter('GET', 'method')) {
            // check for allowed rest commands
            if (in_array(mb_strtoupper($_GET['method']), $allowed_rest_methodnames)) {
                // set the internal (tunneled) method as new REQUEST_METHOD
                self::setRequestMethod($_GET['method']);

                // unset the tunneled method
                unset($_GET['method']);

                // now strip the methodname from the QUERY_STRING and rebuild REQUEST_URI

                // rebuild the QUERY_STRING from $_GET
                $_SERVER['QUERY_STRING'] = http_build_query($_GET);
                // rebuild the REQUEST_URI
                $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
                // append QUERY_STRING to REQUEST_URI if not empty
                if ($_SERVER['QUERY_STRING'] != '') {
                    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                throw new \Koch\Exception\Exception(
                    'Request Method failure. You tried to tunnel a ' . $this->getParameter('method', 'GET')
                    . ' request through an HTTP POST request.'
                );
            }
        } elseif (isset($_SERVER['REQUEST_METHOD']) === true and $_SERVER['REQUEST_METHOD'] == 'GET'
            and $this->issetParameter('GET', 'method')) {
            // NOPE, there's no tunneling through GET!
            throw new \Koch\Exception\Exception(
                'Request Method failure. You tried to tunnel a ' . $this->getParameter('method', 'GET')
                . ' request through an HTTP GET request.'
            );
        }
    }

    /**
     * Get the REQUEST METHOD (GET, HEAD, POST, PUT, DELETE).
     *
     * HEAD request is returned internally as GET.
     * The internally set request_method (PUT or DELETE) is returned first,
     * because we might have a REST-tunneling.
     *
     * @return string request method
     */
    public static function getRequestMethod()
    {
        if (self::$request_method !== null) {
            return self::$request_method;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        // get method from "http method override" header
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) === true) {
            return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }

        // add support for HEAD requests, which are GET requests
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        return $method;
    }

    /**
     * Set the REQUEST_METHOD
     */
    public static function setRequestMethod($method)
    {
        self::$request_method = strtoupper($method);
    }

    /**
     * Checks if a ajax(xhr)-request is given,
     * by checking X-Requested-With Header for XMLHttpRequest.
     *
     * @return boolean true if the request is an XMLHttpRequest, false otherwise
     */
    public static function isAjax()
    {
        if (isset($_SERVER['X-Requested-With']) and $_SERVER['X-Requested-With'] === 'XMLHttpRequest') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * is(GET|POST|PUT|DELETE)
     * Boolean "getters" for several HttpRequest Types.
     * This makes request type checking in controllers easy.
     */

    /**
     * Determines, if request is of type GET
     *
     * @return boolean
     */
    public function isGet()
    {
        return (self::$request_method == 'GET');
    }

    /**
     * Determines, if request is of type POST
     *
     * @return boolean
     */
    public function isPost()
    {
        return (self::$request_method == 'POST');
    }

    /**
     * Determines, if request is of type PUT
     *
     * @return boolean
     */
    public function isPut()
    {
        return (self::$request_method == 'PUT');
    }

    /**
     * Determines, if request is of type DELETE
     *
     * @return boolean
     */
    public function isDelete()
    {
        return (self::$request_method == 'DELETE');
    }

    /**
     * Implementation of SPL ArrayAccess
     * only offsetExists and offsetGet are relevant
     */
    public function offsetExists($offset)
    {
        return $this->issetParameter($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getParameter($offset);
    }

    // not setting request vars
    public function offsetSet($offset, $value)
    {
        return false;
    }

    // not unsetting request vars
    public function offsetUnset($offset)
    {
        return false;
    }
}
