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

namespace Koch\User;

/**
 * Class for User Handling.
 */
class User
{
    /**
     * @var object User Object
     */
    private $user;

    /**
     * Constructor.
     */
    public function __construct(
        /**
         * @var object Koch\Configuration
         */
        private \Koch\Config\Config $config
    )
    {
    }

    /**
     * getUser by user_id.
     *
     * @param int $user_id The ID of the User. Defaults to the user_id from session.
     *
     * @return array $userdata (Dataset of CsUsers + CsProfile)
     */
    public function getUser($user_id = null)
    {
        // init user_id
        if ($user_id === null and $_SESSION['user']['user_id'] > 0) {
            // incomming via session
            $user_id = $_SESSION['user']['user_id'];
        } else {
            // incomming via method parameter
            $user_id = (int) $user_id;
        }

        $userdata = Doctrine_Query::create()
            ->from('CsUsers u')
            ->leftJoin('CsProfiles')
            ->where('u.user_id = ?')
            ->fetchOne([$user_id], Doctrine::HYDRATE_ARRAY);

        if (is_array($userdata)) {
            return $userdata;
        } else {
            return false;
        }
    }

    /**
     * Creates the User-Object and the $session['user'] Array.
     *
     * @param $user_id The ID of the User.
     * @param $email The email of the User.
     * @param $nick The nick of the User.
     */
    public function createUserSession($user_id = '', $email = '', $nick = '')
    {
        // Initialize the User Object
        $this->user = null;

        /*
         * Get User via DB Queries
         *
         * 1) user_id
         * 2) email
         * 3) nick
         */
        if (empty($user_id) === false) {
            // Get the user from the user_id
            $this->user = Doctrine_Query::create()
                #->select('u.*,g.*,o.*')
                ->from('CsUsers u')
                ->leftJoin('u.CsOptions o')
                #->leftJoin('u.CsGroups g')
                ->where('u.user_id = ?')
                ->fetchOne([$user_id], Doctrine::HYDRATE_ARRAY);
        } elseif (empty($email) === false) {
            // Get the user from the email
            $this->user = Doctrine_Query::create()
                #->select('u.*,g.*,o.*')
                ->from('CsUsers u')
                ->leftJoin('u.CsOptions o')
                #->leftJoin('u.CsGroups g')
                ->where('u.email = ?')
                ->fetchOne([$email], Doctrine::HYDRATE_ARRAY);
        } elseif (empty($nick) === false) {
            // Get the user from the nick
            $this->user = Doctrine_Query::create()
                #->select('u.*,g.*,o.*')
                ->from('CsUsers u')
                ->leftJoin('u.CsOptions o')
                #->leftJoin('u.CsGroups g')
                ->where('u.nick = ?')
                ->fetchOne([$nick], Doctrine::HYDRATE_ARRAY);
        }

        /*
         * Check if this user is activated,
         * else reset cookie, session and redirect
         */
        if (is_array($this->user) and $this->user['activated'] === 0) {
            $this->logoutUser();

            // redirect
            $message = _('Your account is not yet activated.');

            \Koch\Http\HttpResponse::redirect('/account/activation_email', 5, 403, $message);
        }

        /*
         * Create $_SESSION['user'] array, containing user data
         */
        if (is_array($this->user)) {
            /*
             * Transfer User Data into Session
             */
            #\Koch\Debug\Debug::firebug($_SESSION);
            #\Koch\Debug\Debug::firebug($this->config);

            $_SESSION['user']['authed']  = 1;
            $_SESSION['user']['user_id'] = $this->user['user_id'];

            $_SESSION['user']['passwordhash'] = $this->user['passwordhash'];
            $_SESSION['user']['email']        = $this->user['email'];
            $_SESSION['user']['nick']         = $this->user['nick'];

            $_SESSION['user']['disabled']  = $this->user['disabled'];
            $_SESSION['user']['activated'] = $this->user['activated'];

            /*
             * SetLanguage
             *
             * At this position the language might already by set by
             * the language_via_get filter. the language value set via GET
             * precedes over the user config and the general config
             * the full order is
             * a) language_via_get filter
             * a) user['language'] from database / personal user setting
             * b) standard language / fallback as defined by $this->config['locale']['locale']
             */
            if (false === isset($_SESSION['user']['language_via_url'])) {
                $_SESSION['user']['language'] = (false === empty($this->user['language']))
                ? $this->user['language']
                : $this->config['locale']['default'];
            }

            /**
             * Frontend-Theme.
             *
             * first take standard theme as defined by $config->theme
             *
             * @todo remove $_REQUEST, frontend theme is selectable via frontend
             */
            if (false === isset($_REQUEST['theme'])) {
                $_SESSION['user']['frontend_theme'] = (!empty($this->user['frontend_theme']))
                ? $this->user['frontend_theme']
                : $this->config['template']['frontend_theme'];
            }

            /*
             * Backend-Theme
             */
            if (empty($this->user['backend_theme']) === false) {
                $_SESSION['user']['backend_theme'] = $this->user['backend_theme'];
            } else {
                $_SESSION['user']['backend_theme'] = $this->config['template']['backend_theme'];
            }

            /*
             * Permissions
             *
             * Get Group & Rights of user_id
             */
            /*
              User-Datensatz beinhaltet ein CsGroups-Array
              user => Array (
              [user_id] => 1
              ...
              [CsGroups] => Array (
              [0] => Array (
              [group_id] => 3
              ...
              [role_id] => 5
              )
              )
              )
             */
            // Initialize User Session Arrays
            $_SESSION['user']['group']  = '';
            $_SESSION['user']['rights'] = '';

            if (false === empty($this->user['CsGroups'])) {
                $_SESSION['user']['group']  = $this->user['CsGroups'][0]['group_id'];
                $_SESSION['user']['role']   = $this->user['CsGroups'][0]['role_id'];
                $_SESSION['user']['rights'] = Koch\ACL::createRightSession(
                    $_SESSION['user']['role'],
                    $this->user['user_id']
                );
            }

            #\Koch\Debug\Debug::firebug($_SESSION);
        } else {
            // this resets the $_SESSION['user'] array
            GuestUser::instantiate();

            #Koch\Debug\Debug::printR($_SESSION);
        }
    }

    /**
     * Check the user.
     *
     * Validates the existance of the user via nick or email and the passwordhash
     * This is done in two steps:
     * 1. check if given nick or email exists
     * and if thats the case
     * 2. compare password from login form with database
     *
     * @param string $login_method contains the login_method ('nick' or 'email')
     * @param string $value        contains nick or email string to look for
     * @param string $passwordhash contains password string
     *
     * @return int ID of User. If the user is found, the $user_id - otherwise false.
     */
    public function checkUser($login_method = 'nick', $value = null, $passwordhash = null)
    {
        $user = null;

        // check if a given nick or email exists
        if ($login_method === 'nick') {
            // get user_id and passwordhash with the nick
            $user = Doctrine_Query::create()
                ->select('u.user_id, u.passwordhash, u.salt')
                ->from('CsUsers u')
                ->where('u.nick = ?')
                ->fetchOne([$value], Doctrine::HYDRATE_ARRAY);
        }

        // check if a given email exists
        if ($login_method === 'email') {
            // get user_id and passwordhash with the email
            $user = Doctrine_Query::create()
                ->select('u.user_id, u.passwordhash, u.salt')
                ->from('CsUsers u')
                ->where('u.email = ?')
                ->fetchOne([$value], Doctrine::HYDRATE_ARRAY);
        }

        $this->moduleconfig = $this->config->readModuleConfig('account');

        // if user was found, check if passwords match each other
        if (true === (bool) $user and true === Koch\Security\Security::checkSaltedHash(
            $passwordhash,
            $user['passwordhash'],
            $user['salt'],
            $this->moduleconfig['login']['hash_algorithm']
        )) {
            // ok, the user with nick or email exists and the passwords matched, then return the user_id
            return $user['user_id'];
        } else {
            // no user was found with this combination of either nick and password or email and password
            return false;
        }
    }

    /**
     * Login.
     *
     * @param int    $user_id      contains user_id
     * @param int    $remember_me  contains remember_me setting
     * @param string $passwordhash contains password string
     */
    public function loginUser($user_id, $remember_me, $passwordhash)
    {
        /*
         * 1. Create the User Data Array and the Session via $user_id
         */
        $this->createUserSession($user_id);

        /*
         * 2. Remember-Me ( set Logindata via Cookie )
         */
        if ($remember_me === true) {
            $this->setRememberMeCookie($user_id, $passwordhash);
        }

        /*
         * 3. user_id is now inserted into the session
         * This transforms the so called Guest-Session to a User-Session
         */
        $this->sessionSetUserId($user_id);

        /*
         * 4. Delete Login attempts
         */
        unset($_SESSION['login_attempts']);

        /*
         * 5. Stats-Updaten
         * @todo stats update after login?
         */
    }

    /**
     * Set the remember me cookie
     * If this cookie is found, the user is re-logged in automatically.
     *
     * @param int    $user_id      contains user_id
     * @param string $passwordhash contains password string
     */
    private function setRememberMeCookie($user_id, $passwordhash)
    {
        // calculate cookie lifetime and combine cookie string
        $cookie_lifetime = time() + round($this->moduleconfig['login']['remember_me_time'] * 24 * 60 * 60);
        $cookie_string   = $user_id . '#' . $passwordhash;

        setcookie('cs_cookie', $cookie_string, $cookie_lifetime);

        unset($cookie_string, $cookie_lifetime);
    }

    /**
     * Logout.
     */
    public function logoutUser()
    {
        // Destroy the old session
        session_regenerate_id(true);

        // Delete cookie
        setcookie('cs_cookie', false);
    }

    /**
     * Checks if a login cookie is set.
     */
    public function checkLoginCookie()
    {
        // Check for login cookie
        if (isset($_COOKIE['cs_cookie'])) {
            $cookie_array    = explode('#', (string) $_COOKIE['cs_cookie']);
            $cookie_user_id  = (int) $cookie_array['0'];
            $cookie_password = $cookie_array['1'];

            #Koch_Module_Controller::initModel('users');

            $this->user = Doctrine_Query::create()
                ->select('u.user_id, u.passwordhash, u.salt')
                ->from('CsUsers u')
                ->where('u.user_id = ?')
                ->fetchOne([$user_id], Doctrine::HYDRATE_ARRAY);

            $this->moduleconfig = $this->config->readModuleConfig('account');

            $hash_ok = Koch\Security::checkSaltedHash(
                $_COOKIE['cs_cookie_password'],
                $this->user['passwordhash'],
                $this->user['salt'],
                $this->moduleconfig['login']['hash_algorithm']
            );

            if (is_array($this->user) and $hash_ok and $_COOKIE['cs_cookie_user_id'] === $this->user['user_id']) {
                // Update the cookie
                $this->setRememberMeCookie($_COOKIE['cs_cookie_user_id'], $_COOKIE['cs_cookie_password']);

                // Create the user session array ($this->session['user'] etc.) by using this user_id
                $this->createUserSession($this->user['user_id']);

                // Update Session in DB
                $this->sessionSetUserId($this->user['user_id']);
            } else {
                // Delete cookies, if no match
                setcookie('cs_cookie_user_id', false);
                setcookie('cs_cookie_password', false);
            }
        }
    }

    /**
     * Sets user_id to current session.
     *
     * @param $user_id int The user_id to set to the session.
     */
    public function sessionSetUserId($user_id)
    {
        $result = Doctrine_Query::create()
            ->select('user_id')
            ->from('CsSession')
            ->where('session_id = ?')
            ->fetchOne([session_id()]);

        /*
         * Update Session, because we know that session_id already exists
         */
        if ($result) {
            $result->user_id = $user_id;
            $result->save();

            return true;
        }

        return false;
    }

    /**
     * Checks, if the user is authorized to access a resource.
     * It's a proxy method forwarding to Authorization::isAuthorized().
     *
     * @param string $module     Module name, e.g. 'guestbook'.
     * @param string $permission Permission name, e.g. 'actionList'.
     *
     * @return bool True, if the user is authorized. Otherwise, false.
     */
    public static function isAuthorized($module = '', $permission = '')
    {
        return \Koch\User\Authorization\Authorization::isAuthorized($module, $permission);
    }

    /**
     * Deletes all USERS which have joined but are not activated after 3 days.
     *
     * 259200 = (60s * 60m * 24h * 3d)
     */
    public function deleteJoinedButNotActivitatedUsers()
    {
        Doctrine_Query::create()
            ->delete('CsUsers')
            ->from('CsUsers')
            ->where('activated = ? AND joined < ?')
            ->execute([0, time() - 259200]);
    }

    /**
     * Check, whether a user is authenticated (logged in).
     *
     * @return bool Returns Tru,e if user is authenticated. Otherwise, false.
     */
    public function isUserAuthenticated()
    {
        if (true === isset($_SESSION['user']['authenticated']) and true === (bool) $_SESSION['user']['authenticated']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the user_id from Session.
     *
     * @return int user_id
     */
    public function getUserIdFromSession()
    {
        return $_SESSION['user']['user_id'];
    }
}
