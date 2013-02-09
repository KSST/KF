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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Filter for Session Security.
 *
 * Purpose:
 * This Filter ensures the session integrity.
 * It will destroy the current session and redirect to login, on the following conditions:
 *
 * 1) IP changed
 * 2) Browser changed
 * 3) Host changed
 * 4) wrong passwords where tried a number of times
 */
class SessionSecurity implements FilterInterface
{
    private $config     = null;

    public function __construct(Koch\Config $config)
    {
        $this->config     = $config;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $this->response = $response;

        /**
         * 1. Check for IP
         */

        if ($this->config['session']['check_ip'] == true) {
            if ( !isset($_SESSION['client_ip']) ) {
                $_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'];
            } elseif ($_SERVER['REMOTE_ADDR'] != $_SESSION['client_ip']) {
                session_destroy(session_id());

                $this->response->redirect('index.php?mod=login');
            }
        }

        /**
         * 2. Check for Browser
         */

        if ($this->config['session']['check_browser'] == true) {
            if ( !isset($_SESSION['client_browser']) ) {
                $_SESSION['client_browser'] = $_SERVER['HTTP_USER_AGENT'];
            } elseif ($_SERVER['HTTP_USER_AGENT'] != $_SESSION['client_browser']) {
                session_unset();
                session_destroy();

                $this->response->redirect('index.php?mod=login');
            }
        }

        /**
         * 3. Check for Host Address
         */

        if ($this->config['session']['check_host'] == true) {
            if ( isset($_SESSION['client_host']) === false ) {
                $_SESSION['client_host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            } else {
                if (gethostbyaddr($_SERVER['REMOTE_ADDR']) != $_SESSION['client_host']) {
                    session_unset();
                    session_destroy();

                    $this->response->redirect('index.php?mod=login');
                }
            }
        }

        /**
         * 4. Check maximal password tries
         */

        // take the initiative, if maximal_password_tries is enabled (greater 0)in Koch\Config
        // or pass to the next filter / do nothing
        /*if ($this->config['session']['maximal_password_tries'] > 0) {
            // if PW_TRIES is lower than the configvalue
            if ($_SESSION['PW_TRIES'] < $this->config['session']['maximal_password_tries']) {
                // check, if a form field input $_POST['password'] exists
                if (true == $this->request->issetParameter('POST','password')) {
                    // if PW_TRIES does not exist, it's the first try of a password
                    if (!isset($_SESSION['PW_TRIES'])) {
                        $_SESSION['PW_TRIES'] = 1;
                    }

                    // if PW_TRIES exists, and is lower or equal to max_tries configvalue, then increase it
                    if ($_SESSION['PW_TRIES'] <= $this->config['session']['maximal_password_tries']) {
                        $_SESSION['PW_TRIES']++;
                    }

                    // @todo
                    // EVENT => check the password provided, if invalid show the password form again here.
                } else {
                    // reset our session variables.
                    unset($_SESSION['PW_TRIES']);
                }
            }
        }// else => bypass */
    }
}
