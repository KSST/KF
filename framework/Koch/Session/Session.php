<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Session;

use Koch\Exception\Exception;

/**
 * Koch Framework - Class for Session Handling
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Session
 */
class Session implements SessionInterface, \ArrayAccess /* 5.4 implements SessionHandlerInterface */
{
    // stop applications to influcence each other by applying a session_name
    const SESSION_NAME = 'CsuiteSID';

    /**
     * Session Expire time in seconds.
     * 1800 seconds / 60 = 30 Minutes
     *
     * @var integer
     */
    public $session_expire_time = 1800;

    /**
     * Probabliity of trashing the Session as percentage.
     * (This implies that gc_divisor is fixed to 100.)
     *
     * @var integer
     */
    public $session_probability = 10;

    /**
     * @var array
     */
    private $config = array();

    /**
     * This creates the session.
     *
     * Injections:
     * Koch\Config is needed for the configuration of session variables.
     * HttpRequest is needed to determine the current location of the user on the website.
     *
     * @todo reading and writing the session are transactions! implement
     *
     * Overwrite php.ini settings
     * Start the session
     * @param object Koch\Config
     * @param object Koch_HttpRequest
     */

    public function __construct(\Koch\Config\Config $config)
    {
        $this->config = $config;
        var_dump($config);

        /**
         * Set the Session Expire Time.
         * The value comming from the clansuite config and is a minute value.
         */
        if (isset($this->config['session']['session_expire_time'])
            and $this->config['session']['session_expire_time'] <= 60) {
            $this->session_expire_time = $this->config['session']['session_expire_time'] * 60;
        }

        // configuration not needed any longer, free some memory
        unset($this->config);

        /**
         * Configure Session
         */
        ini_set('session.name', self::SESSION_NAME);
        ini_set('session.save_handler', 'user');

        /**
         * Configure Garbage Collector
         * This will call the GC in 10% of the requests.
         * Calculation : gc_probability/gc_divisor = 10/100 = 0,1 = 10%
         */
        ini_set('session.gc_maxlifetime', $this->session_expire_time);
        ini_set('session.gc_probability', $this->session_probability);
        ini_set('session.gc_divisor', 100);

        // use_trans_sid off -> because spiders will index with PHPSESSID
        // use_trans_sid on  -> considered evil
        ini_set('session.use_trans_sid', 0);

        // @todo check if there is a problem with rewriting
        #ini_set('url_rewriter.tags'         , "a=href,area=href,frame=src,form=,formfieldset=");
        // use a cookie to store the session id (no session_id's in URL)
        // session cookies are forced!
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        // stop javascript accessing the cookie (XSS)
        ini_set('session.cookie_httponly', 1);

        /**
         * Setup the custom session handler methods
         * Userspace Session Storage
         */
        session_set_save_handler(
            array($this, 'sessionOpen'),
            array($this, 'sessionClose'),
            array($this, 'sessionRead'),
            array($this, 'sessionWrite'),
            array($this, 'sessionDestroy'),
            array($this, 'sessionGc')
        );

        // prevents unexpected effects when using objects as save handlers
        register_shutdown_function('session_write_close');

        // Start Session
        self::startSession($this->session_expire_time);

        // Apply and Check Session Security
        self::validateAndSecureSession();
    }

    /**
     * Start Session and throw Error on failure
     */
    private static function startSession($time = 1800)
    {
        // set cookie parameters
        session_set_cookie_params($time);

        // START THE SESSION
        if (true === session_start()) {
            // Set Cookie + adjust the expiration time upon page load
            setcookie(self::SESSION_NAME, session_id(), time() + $time, '/');
        } else {
            throw new \Koch\Exception\Exception('The session start failed!', 200);
        }
    }

    /**
     * Change from a permissive to a strict session system by applying
     * several security flags.
     *
     * A new session ID is created when
     * a) if session string-lenght corrupted
     * b) OR not initiated already
     * c) OR application token missing
     */
    private static function validateAndSecureSession()
    {
        if (mb_strlen(session_id()) != 32 or false === isset($_SESSION['application']['initiated'])) {
            /**
             * Make a new session_id and destroy old session
             *
             * From PHP 5.1 on, if set to true, it will force the
             * session extension to remove the old session on an id change.
             */
            session_regenerate_id(true);

            // session fixation
            $_SESSION['application']['initiated'] = true;

            /**
             * Session Security Token
             * CSRF: http://shiflett.org/articles/cross-site-request-forgeries
             */
            // session token
            $_SESSION['application']['token'] = md5(uniqid(rand(), true));

            // session time
            $_SESSION['application']['token_time'] = time();
        }
    }

    /**
     * =========================================
     *      Custom Session Handler Methods
     * =========================================
     */

    /**
     * Opens a session
     *
     * @return true
     */
    public function sessionOpen()
    {
        return true;
    }

    /**
     * Closes a session
     *
     * @return true
     */
    public function sessionClose()
    {
        session_write_close();

        return true;
    }

    /**
     * Reads a session
     *
     * @param  integer $session_id contains the session_id
     * @return string  string of the session data
     */
    public function sessionRead($session_id)
    {
        try {
            $em = \Clansuite\Application::getEntityManager();
            $query = $em->createQuery(
                'SELECT s.session_data, s.session_starttime
                FROM \Entity\Session s
                WHERE s.session_name = :name
                AND s.session_id = :id'
            );
            $query->setParameters(array('name' => self::SESSION_NAME, 'id' => $session_id));
            $result = $query->getResult();

            if ($result) {
                return (string) $result[0]['session_data'];  // unserialize($result['session_data']);
            }
        } catch (Exception $e) {
            $msg = '';

            if (defined('DEBUG') and DEBUG == true) {
                $msg .= get_class($e) . ' thrown within the session handler.';
                $msg .= '<br /> Message: ' . $e->getMessage();
            }

            $uri = sprintf(
                'http://%s%s',
                $_SERVER['SERVER_NAME'],
                dirname($_SERVER['PHP_SELF']) . 'installation/index.php'
            );
            $uri = str_replace('\\', '/', $uri);

            $msg .= '<p><b><font color="#FF0000">[Koch Framework Error] ';
            $msg .= _('The database table for sessions is missing.');
            $msg .= '</font></b> <br />';
            $msg .= _('Please use <a href="%s">Installation</a> to perform a proper installation.');
            $msg .= '</p>';

            throw new Exception(sprintf($msg, $uri));
        }
    }

    /**
     * Write a session
     *
     * This redefines php's session_write_close()
     *
     * @param  integer $session_id contains session_id
     * @param  array   $data       contains session_data
     * @return bool
     */
    public function sessionWrite($session_id, $data)
    {
        /**
         * Try to INSERT Session Data or REPLACE Session Data in case session_id already exists
         */
        $em = \Clansuite\Application::getEntityManager();

        $query = $em->createQuery(
            'UPDATE \Entity\Session s
                SET s.session_id = :id,
                s.session_name = :name,
                s.session_starttime = :time,
                s.session_data = :data,
                s.session_visibility = :visibility,
                s.session_where = :where,
                s.user_id = :user_id
                WHERE s.session_id = :id'
        );

        $query->setParameters(
            array(
                'id' => $session_id,
                'name' => self::SESSION_NAME,
                'time' => (int) time(),
                'data' => $data, // @todo serialize($data)
                'visibility' => '1', // @todo ghost mode
                'where' => 'session_start',
                'user_id' => '0'
            )
        );

        $query->execute();

        return true;
    }

    /**
     * Destroy the current session.
     *
     * This redefines php's session_destroy()
     *
     * @param string $session_id
     */
    public function sessionDestroy($session_id)
    {
        // Unset all of the session variables.
        $_SESSION = array();

        //  Unset Cookie Vars
        if (isset($_COOKIE[self::SESSION_NAME]) === true) {
            setcookie(self::SESSION_NAME, false);
        }

        /**
         * Delete session from DB
         */
        $em = \Clansuite\Application::getEntityManager();

        $query = $em->createQuery(
            'DELETE \Entity\Session s
            WHERE s.session_name = :name
            AND s.session_id = :id'
        );

        $query->setParameters(
            array(
                'name' => self::SESSION_NAME,
                'id' => $session_id
            )
        );

        $query->execute();
    }

     /**
     * Session Garbage Collector
     *
     * Removes the current session, if:
     * a) gc probability is reached (ini_set)
     * b) time() is reached (DB has timestamp stored, that is time() + expiration )
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime  1800 = 30*60
     * @see session.gc_probability    1
     * @usage execution rate 1/100 (session.gc_probability/session.gc_divisor)
     *
     * @param int session life time (mins)
     * @return boolean
     */
    public function sessionGc($maxlifetime = 30)
    {
        if ($maxlifetime == 0) {
            return;
        }

        /**
         * Determine expiration time of the session
         *
         * $maxlifetime is a minute time value
         * its fetched from $config['session']['session_expire_time']
         * $sessionLifetime is in seconds
         */
        $sessionlifetime = $maxlifetime * 60;
        $expire_time = time() + $sessionlifetime;

        $em = \Clansuite\Application::getEntityManager();

        $query = $em->createQuery(
            'DELETE \Entity\Session s
            WHERE s.session_name = :name
            AND s.session_starttime < :time'
        );

        $query->setParameters(
            array(
                'name' => self::SESSION_NAME,
                'time' => (int) $expire_time
            )
        );

        $query->execute();

        return true;
    }

    /**
     * =======================
     *       Get and Set
     * =======================
     */

    /**
     * Sets Data into the Session.
     *
     * @param string key
     * @param mixed  value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets Data from the Session.
     *
     * @param string key
     * @return mixed value/boolean false
     */
    public function get($key)
    {
        if ($_SESSION[$key] !== null) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    /**
     * =====================================
     *   Implementation of SPL ArrayAccess
     * =====================================
     */

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($_SESSION[$offset]) === true) {
            return $_SESSION[$offset];
        } else {
            throw new \InvalidArgumentException(sprintf('Array Key "%s" is not defined.', $offset));
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    // @todo note by vain: check if this works on single array of session?
    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);

        return true;
    }
}
