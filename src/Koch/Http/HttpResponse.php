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

namespace Koch\Http;

/**
 * Class for Response Handling.
 *
 * This class represents the web response object on a request processed by Koch Framework.
 */
class HttpResponse implements HttpResponseInterface
{
    /**
     * Status of the response as integer value.
     * $statusCode = '200' => 'OK'.
     *
     * @var int
     */
    private static $statusCode = '200';

    /**
     * @var array Array holding the response headers.
     */
    private static $headers = [];

    /**
     * @var string String holding the response content (body).
     */
    private static $content;

    /**
     * @var string String holding the content type.
     */
    private static $content_type = 'text/html';

    /**
     * Sets the HTTP Status Code for this response.
     * This method is also used to set the return status code when there
     * is no error (for example for the status codes 200 (OK) or 301 (Moved permanently) ).
     *
     * @param int $statusCode The status code to set
     */
    public static function setStatusCode($statusCode)
    {
        self::$statusCode = (string) $statusCode;
    }

    /**
     * Returns the HTTP Status Code.
     *
     * @return int HTTP Status Code.
     */
    public static function getStatusCode()
    {
        return self::$statusCode;
    }

    /**
     * Returns the HTTP 1.1 status code description for a given status code.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    public static function getStatusCodeDescription($statusCode)
    {
        // Array holding some often occuring status descriptions.
        static $statusCodes = [
           // Successful
           '200' => 'OK',
           '201' => 'Created',
           '202' => 'Accepted',
           // Redirection
           '301' => 'Moved Permanently',
           '302' => 'Found',
           '303' => 'See Other',
           '304' => 'Not Modified',
           '307' => 'Temporary Redirect',
           // Client Error
           '400' => 'Bad Request',
           '401' => 'Unauthorized',
           '403' => 'Forbidden',
           '404' => 'Not Found',
           // Server Error
           '500' => 'Internal Server Error',
           '502' => 'Bad Gateway',
           '503' => 'Service Temporarily Unavailable',
        ];

        return $statusCodes[$statusCode];
    }

    /**
     * Adds a header to the response array, which is send to the browser.
     *
     * @param string $name  the name of the header
     * @param string $value the value of the header
     */
    public static function addHeader($name, $value)
    {
        self::$headers[$name] = $value;
    }

    /**
     * setContent appends or replaces the content of the http response buffer.
     *
     * appends content to the response body.
     * when $replace is true, the bodycontent is replaced.
     *
     * @param string $content Content to store in the buffer
     * @param bool   $replace Toggle between append or replace.
     */
    public static function setContent($content, $replace = false)
    {
        // check, if the content should be replaced
        if ($replace === false) {
            // no, just append the content
            self::$content .= $content;
        } else {
            // replace the body with the content
            self::$content = $content;
        }
    }

    /**
     * get content retunrs the response body.
     */
    public static function getContent()
    {
        return self::$content;
    }

    /**
     * Set the content type.
     *
     * @param string $type Content type: html, txt, xml, json.
     *
     * @return string
     */
    public static function setContentType($type = 'html', $charset = 'UTF-8')
    {
        $types = [
            'csv'  => 'text/csv',
            'html' => 'text/html',
            'txt'  => 'text/plain',
            'xml'  => 'application/xml',
            'rss'  => 'application/rss+xml',
            'json' => 'application/json',
            'js'   => 'application/javascript',
        ];

        if (isset($types[$type]) === false) {
            throw new \InvalidArgumentException('Specified type not valid. Use: html, txt, xml or json.');
        }

        #addHeader('Content-Type', $type . ($charset ? '; charset='.$charset.': ''));
        self::$content_type = $types[$type];
    }

    /**
     * Returns the content type for insertion into the header.
     *
     * @return string A content type, like "application/json" or "text/html".
     */
    public static function getContentType()
    {
        return self::$content_type;
    }

    /**
     * This flushes the headers and bodydata to the client.
     */
    public static function sendResponse()
    {
        // save session before exit
        if ((bool) session_id()) {
            session_write_close();
        }

        // activateOutputCompression when not in debugging mode
        if (defined('DEBUG') and DEBUG === false) {
            \Koch\Http\ResponseCompression::startBuffer();
        }

        // Send the status line
        self::addHeader('HTTP/1.1', self::$statusCode . ' ' . self::getStatusCodeDescription(self::$statusCode));

        // Suppress Framesets
        self::addHeader('X-Frame-Options', 'deny'); // not SAMEORIGIN

        // Send our Content-Type with UTF-8 encoding
        self::addHeader('Content-Type', self::getContentType() . '; charset=UTF-8');

        // Send user specificed headers from self::$headers array
        if (false === headers_sent()) {
            foreach (self::$headers as $name => $value) {
                header($name . ': ' . $value, false);
            }
        }

        // make it possible to attach HTML content to the body directly before flushing the response
        // \Your\Application::triggerEvent('onBeforeResponse', array('content' => self::$content));

        // Finally echo the response body
        echo self::getContent();

        if (defined('DEBUG') and DEBUG === false) {
            \Koch\Http\ResponseCompression::flushCompressedBuffer();
        }

        // OK, Reset -> Package delivered! Return to Base!
        self::clearHeaders();

        return true;
    }

    /**
     * Resets the headers and content.
     *
     * @return bool true.
     */
    public static function clearHeaders()
    {
        self::$headers = [];
        self::$content = null;

        return true;
    }
    /**
     * A better alternative (RFC 2109 compatible) to the php setcookie() function.
     *
     * @param string Name of the cookie
     * @param string Value of the cookie
     * @param int Lifetime of the cookie
     * @param string Path where the cookie can be used
     * @param string Domain which can read the cookie
     * @param bool Secure mode?
     * @param bool Only allow HTTP usage? (PHP 5.2)
     *
     * @return bool Cookie set.
     */
    public static function setCookie($name, $value = '', $maxage = 0, $path = '', $domain = '', $secure = false, $HTTPOnly = false)
    {
        $ob = ini_get('output_buffering');

        // Abort the method if headers have already been sent, except when output buffering has been enabled
        if (headers_sent() && (bool) $ob === false or mb_strtolower($ob) === 'off') {
            return false;
        }

        if (false === empty($domain)) {
            // Fix the domain to accept domains with and without 'www.'.
            if (mb_strtolower(mb_substr((string) $domain, 0, 4)) === 'www.') {
                $domain = mb_substr((string) $domain, 4);
            }

            // Add the dot prefix to ensure compatibility with subdomains
            if (mb_substr((string) $domain, 0, 1) !== '.') {
                $domain = '.' . $domain;
            }

            // Remove port information.
            $port = mb_strpos((string) $domain, ':');

            if ($port !== false) {
                $domain = mb_substr((string) $domain, 0, $port);
            }
        }

        header(
            'Set-Cookie: ' . rawurlencode((string) $name) . '=' . rawurlencode((string) $value)
            . (true === empty($domain) ? '' : '; Domain=' . $domain)
            . (true === empty($maxage) ? '' : '; Max-Age=' . $maxage)
            . (true === empty($path) ? '' : '; Path=' . $path)
            . (false === $secure ? '' : '; Secure')
            . (false === $HTTPOnly ? '' : '; HttpOnly'),
            false
        );

        return true;
    }

    /**
     * Deletes a cookie.
     *
     * @param string $name   Name of the cookie
     * @param string $path   Path where the cookie is used
     * @param string $domain Domain of the cookie
     * @param bool Secure mode?
     * @param bool Only allow HTTP usage? (PHP 5.2)
     */
    public static function deleteCookie($name, $path = '/', $domain = '', $secure = false, $httponly = null)
    {
        // expire = 324993600 = 1980-04-19
        setcookie($name, '', ['expires' => 324993600, 'path' => $path, 'domain' => $domain, 'secure' => $secure, 'httponly' => $httponly]);
    }

    /**
     * Sets NoCache Header Values.
     */
    public static function setNoCacheHeader()
    {
        // set nocache via session
        #session_cache_limiter('nocache');
        // reset pragma header
        self::addHeader('Pragma', 'no-cache');
        // reset cache-control
        self::addHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        // append cache-control
        self::addHeader('Cache-Control', 'post-check=0, pre-check=0');
        // force immediate expiration
        self::addHeader('Expires', '1');
        // set date of last modification
        self::addHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
    }

    /**
     * Detects a flashmessage tunneling via the redirect messagetext.
     *
     * @param string $message Redirect Message ("flashmessagetype#message text")
     */
    public static function detectTypeAndSetFlashmessage($message)
    {
        // detect if a flashmessage is tunneled
        if ($message !== null and true === (bool) strpos($message, '#')) {
            //  split at tunneling separator
            $array = explode('#', $message);
            // results in: array[0] = type and array[1] = message)
            \Koch\Session\FlashMessages::setMessage($array[0], $array[1]);
            // return the message
            return $array[1];
        }
    }

    /**
     * Redirect.
     *
     * Redirects to another action after disabling the caching.
     * This avoids the typical reposting after an POST is send by disabling the cache.
     * This enables the POST-Redirect-GET Workflow.
     *
     * @param string Redirect to this URL
     * @param int    seconds before redirecting (for the html tag "meta refresh")
     * @param int    http status code, default: '303' => 'See other'
     * @param string redirect message
     */
    public static function redirectNoCache($url, $time = 0, $statusCode = 303, $message = '')
    {
        self::setNoCacheHeader();
        self::redirect($url, $time, $statusCode, $message);
    }

    /**
     * Redirect, redirects to an URL.
     * This redirects automatically, when headers are not already sent,
     * else it provides a link to the target URL for manual redirection.
     *
     * Time defines how long the redirect screen will be displayed.
     * Statuscode defines a http status code. The default value is 303.
     * Text is a message string for the html body of the redirect screen.
     *
     * @param string Redirect to this URL
     * @param int    seconds before redirecting (for the html tag "meta refresh")
     * @param int    http status code, default: '303' => 'See other'
     * @param text   text of redirect message
     * @param string redirect mode LOCATION, REFRESH, JS, HTML
     * @param string $mode
     */
    public static function redirect($url, $time = 0, $statusCode = 303, $message = null, $mode = null)
    {
        // convert from internal slashed format to external URL
        $url = \Koch\Router\Router::buildURL($url, false);

        $filename      = '';
        $linenum       = '';
        $redirect_html = '';

        // redirect only, if headers are NOT already send
        if (headers_sent($filename, $linenum) === false) {
            // clear all output buffers
            #while(@ob_end_clean());

            // redirect to ...
            self::setStatusCode($statusCode);

            // detect if redirect message contains a flashmessage type
            // fetch message from "type#message"
            $message = self::detectTypeAndSetFlashmessage($message);

            switch ($mode) {
                default:
                case 'LOCATION':
                    header('LOCATION: ' . $url);
                    #session_write_close(); // @todo figure out, if session closing is needed?
                    \Koch\Tools\ApplicationQuit::quit();
                    break;
                case 'REFRESH':
                    header('Refresh: 0; URL="' . $url . '"');
                    #session_write_close(); // @todo figure out, if session closing is needed?
                    break;
                case 'JS':
                    $redirect_html = '<script type="text/javascript">window.location.href=' . $url . ';</script>';
                    break;
                case 'HTML':
                    // redirect html content
                    $redirect_html = '<html><head>';
                    $redirect_html .= '<meta http-equiv="refresh" content="' . $time . '; URL=' . $url . '" />';
                    $redirect_html .= '</head><body>' . $message . '</body></html>';
                    break;
            }

            if (($redirect_html === '' || $redirect_html === '0') === false) {
                #self::addHeader('Location', $url);
                self::setContent($redirect_html, $time);
            }

            // Flush the content on the normal way!
            self::sendResponse();
        } else { // headers already send!
            $msg = _('Header already send in file %s in line %s. Redirecting impossible.');
            $msg .= _('You might click this link instead to redirect yourself to the <a href="%s">target url</a> an');
            sprintf($msg, $filename, $linenum, $url);
            \Koch\Tools\ApplicationQuit::quit();
        }
    }
}
