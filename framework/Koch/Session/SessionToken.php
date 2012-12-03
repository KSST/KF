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

namespace Koch\Session;

use Koch\Exception\Exception;

class SessionToken extends Session
{
    /**
     * Get a CSRF Token name.
     *
     * @return string
     */
    public function getTokenName()
    {
        $tokenName = $this->session->get('_token_name');
        if (!$tokenName) {
            $tokenName = 'TOKEN' . mt_rand();
            $this->session->set('_token_name', $tokenName);
        }

        return $tokenName;
    }

    /**
     * Get a CSRF Token value as stored in the session, or create one if it doesn't yet exist
     *
     * @return string
     */
    public function getToken()
    {
        $tokenName = $this->getTokenName();
        $token = $this->session->get('_' . $tokenName);

        if (empty($token)) {
            $token = md5(uniqid(rand(), TRUE));
            $this->session->set('_' . $tokenName, $token);
            $this->session->set('_token_time', time());
        }

        return $token;
    }

    public function getTokenTime()
    {
        return ;
    }

    /**
     * Validates the token.
     *
     * Returns true, if the current AJAX GET request contains a valid CSRF token, false if not.
     * Returns true, if the current POST request contains a valid CSRF token, false if not.
     *
     * @return bool
     */
    public function validateToken()
    {
        $tokenName = $this->getTokenName();
        $token = $this->getToken();

        // is token outdated?
        if ($this->session->get('_token_time') + $this->config->session_token_maxtime <= time()) {
            return false;
        }

        if ($this->config->ajax && isset($_SERVER["HTTP_X_$tokenName"]) && $_SERVER["HTTP_X_$tokenName"] === $token) {
            return true;
        }
        if ($this->input->post($tokenName) === $token) {
            return true;
        }

        // the token is not valid
        return false;
    }

    /**
     * Validates the token and throws an exception, if the token is invalid.
     */
    public function validate()
    {
        if (!$this->config->protectCSRF) {
            return true;
        }
        if ($this->validateToken()) {
            return true;
        }
        $this->resetToken();
        throw new Exception($this->_('Request aborted. Session Token not valid.'));
    }

    /**
     * Remove token from session.
     */
    public function resetToken()
    {
        $tokenName = $this->getTokenName();
        $this->session->remove('_token_name');
        $this->session->remove('_token_time');
        $this->session->remove('_' . $tokenName);
    }
}
