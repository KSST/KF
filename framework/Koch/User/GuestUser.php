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

namespace Koch\User;

/**
 * Guest user.
 *
 * The object represents the initial values of the user object.
 * This is the guest visitor.
 */
class GuestUser
{
    /**
     * @var object Koch_GuestUser is a singleton.
     */
    private static $instance = null;

    /**
     * @var object Koch\Configuration
     */
    private $config = null;

    /**
     * @todo get rid of singleton
     * @return This object is a singleton.
     */
    public static function instantiate()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->config = \Clansuite\Application::getInjector()->instantiate('Koch\Config\Config');

        /**
         * Fill $_SESSION[user] with Guest-User-infos
         */

        $_SESSION['user']['authed']         = 0;  // a guest is not authed
        $_SESSION['user']['user_id']        = 0;  // a guest has the user_id 0
        $_SESSION['user']['nick']           = _('Guest'); // a guest has the nickname "Guest"

        $_SESSION['user']['passwordhash']   = '';
        $_SESSION['user']['email']          = '';

        $_SESSION['user']['disabled']       = 0;
        $_SESSION['user']['activated']      = 0;

        /**
         * Language for Guests
         *
         * This sets the default locale as defined by the main configuration value ([language][locale]).
         * The value is set as session value [user][language] for the user,
         * if another language was not set via the GET request (URL),
         * and processed in the filter (like "index.php?lang=fr").
         */
        if (!isset($_SESSION['user']['language_via_url']) and isset($this->config['locale']['locale']) === true) {
            $_SESSION['user']['language'] = $this->config['locale']['locale'];
        }

        /**
         * Theme for Guests
         *
         * Sets the Default Theme for all Guest Visitors, if not already set via a GET request.
         * Theme for Guest Users as defined by config['template']['frontend_theme']
         */
        if (empty($_SESSION['user']['frontend_theme']) and isset($this->config['template']['frontend_theme']) === true) {
            $_SESSION['user']['frontend_theme'] = $this->config['template']['frontend_theme'];
        }

        // @todo remove this line, when user login is reactivated
        $_SESSION['user']['backendend_theme'] = 'admin';

        /**
         * Permissions for Guests
         */

        $_SESSION['user']['group']  = 1; // @todo hardcoded for now
        $_SESSION['user']['role']   = 3;
        #$_SESSION['user']['rights'] = Koch_ACL::createRightSession( $_SESSION['user']['role'] );

        #\Koch\Debug\Debug::printR($_SESSION);
        #\Koch\Debug\Debug::firebug($_SESSION);
    }
}
