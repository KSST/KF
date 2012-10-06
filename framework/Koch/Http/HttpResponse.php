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

namespaces, the methodname can not be setCookie()
     *       because this would conflict with the php function name.
     */
    public static function createCookie($name, $value='', $maxage = 0, $path='', $domain='', $secure = false, $HTTPOnly = false)
    {
        $ob = ini_get('output_buffering');

        // Abort the method if headers have already been sent, except when output buffering has been enabled
        if ( headers_sent() and (bool) $ob === false or mb_strtolower($ob) == 'off' ) {
            return false;
        }

        if (false === empty($domain) ) {
            // Fix the domain to accept domains with and without 'www.'.
            if ( mb_strtolower( mb_substr($domain, 0, 4) ) === 'www.' ) {
                $domain = mb_substr($domain, 4);
            }

            // Add the dot prefix to ensure compatibility with subdomains
            if ( mb_substr($domain, 0, 1) !== '.' ) {
                $domain = '.'.$domain;
            }

            // Remove port information.
            $port = mb_strpos($domain, ':');

            if ($port !== false) {
                $domain = mb_substr($domain, 0, $port);
            }
        }

        header('Set-Cookie: '.rawurlencode($name).'='.rawurlencode($value)
                                    .(true === empty($domain) ? '' : '; Domain='.$domain)
                                    .(true === empty($maxage) ? '' : '; Max-Age='.$maxage)
                                    .(true === empty($path) ? '' : '; Path='.$path)
                                    .(false === $secure ? '' : '; Secure')
                                    .(false === $HTTPOnly ? '' : '; HttpOnly'), false);

        return true;
    }

    /**
     * Deletes a cookie
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
        setcookie($name, '', 324993600, $path, $domain, $secure, $httponly);
    }

    /**
     * Sets NoCache Header Values
     */
    public static function setNoCacheHeader()
    {
        // set nocache via session
        #session_cache_limiter('nocache');

        // reset pragma header
        self::addHeader('Pragma',        'no-cache');
        // reset cache-control
        self::addHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        // append cache-control
        self::addHeader('Cache-Control', 'post-check=0, pre-check=0');
        // force immediate expiration
        self::addHeader('Expires',       '1');
        // set date of last modification
        self::addHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
    }

    /**
     * Detects a flashmessage tunneling via the redirect messagetext
     *
     * @param string $message Redirect Message ("flashmessagetype#message text")
     */
    public static function detectTypeAndSetFlashmessage($message)
    {
        // detect if a flashmessage is tunneled
        if ( $message !== null and true === (bool) strpos($message, '#')) {
            //  split at tunneling separator
            $array = explode('#', $message);
            // results in: array[0] = type and array[1] = message)
            Koch_Flashmessages::setMessage($array[0], $array[1]);
            // return the message
            return $array[1];
        }
    }

    /**
     * Redirect
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
     * Redirect
     *
     * Redirects to the URL.
     * This redirects automatically, when headers are not already sent,
     * else it provides a link to the target URL for manual redirection.
     *
     * Time defines how long the redirect screen will be displayed.
     * Statuscode defines a http status code. The default value is 302.
     * Text is a messagestring for the htmlbody of the redirect screen.
     *
     * @param string Redirect to this URL
     * @param int    seconds before redirecting (for the html tag "meta refresh")
     * @param int    http status code, default: '303' => 'See other'
     * @param text   text of redirect message
     * @param string redirect mode LOCATION, REFRESH, JS, HTML
     */
    public static function redirect($url, $time = 0, $statusCode = 303, $message = null, $mode = null)
    {
        // convert from internal slashed format to external URL
        $url = Koch_Router::buildURL($url, false);

        $filename = '';
        $linenum = '';
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
                    header('LOCATION: '. $url);
                    #session_write_close(); // @todo figure out, if session closing is needed?
                    exit();
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

            if (empty($redirect_html) === false) {
                #self::addHeader('Location', $url);
                self::setContent($redirect_html, $time, htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));
            }

            // Flush the content on the normal way!
            self::sendResponse();
        } else { // headers already send!
            $msg  = _('Header already send in file %s in line %s. Redirecting impossible.');
            $msg .= _('You might click this link instead to redirect yourself to the <a href="%s">target url</a> an');
            sprintf($msg, $filename, $linenum, $url);
            exit;
        }
    }
}
