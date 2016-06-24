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

namespace Koch\Session;

/**
 * Flashmessages.
 *
 * The sending of messages is very simple for GET-Requests.
 * You can use echo or the template to output the messages.
 * It's not that simple for POST-Requests followed by a redirect.
 * In general there are three solutions:
 * You can (1) append the message to the URL, which results in very long urls
 * like header('Location: http://www.example.com/index.php?message='.urlencode($message));
 * or you can (2) implement a two step redirect, first redirecting to a successview (html with message
 * and then redirecting to the target url but you can (3) store it in the usersession.
 * This class implements the third solution.
 *
 * Think of this class as an improvement and extension of the user session object.
 * This object containers messages for the user across different HTTP-Requests.
 * by storing them in the users-session.
 * Typical messages are: errors, notices, warnings and status notifications.
 * These will flash (hence the name) on the request and inform the user.
 * The message is removed from session after itï¿½s been displayed.
 *
 * Inspired by Ruby on Rails Flash Messages.
 */
class FlashMessages
{
    /**
     * @var contains array of
     */
    private static $flashmessages = [];

    /**
     * @var array types of flashmessages (whitelist)
     */
    private static $flashmessagetypes = [
        'error', 'warning', 'notice', 'success', 'debug',
    ];

    /**
     * Sets a message to the session.
     *
     * Supported Messagetypes are error, warning, notice, success, debug
     *
     * @see self::$flashmessagetypes
     *
     * @param $message the actual message
     * @param $type string Type of the message, usable for formatting. Defaults to notice.
     */
    public static function setMessage($message, $type = 'notice')
    {
        if (in_array($type, self::$flashmessagetypes, true)) {
            self::$flashmessages[$type][]      = $message;
            $_SESSION['user']['flashmessages'] = self::$flashmessages;
        }
    }

    /**
     * Sets an error flash message.
     *
     * @param string $message
     */
    public static function setErrorMessage($message)
    {
        self::setMessage($message, 'error');
    }

    /**
     * Returns the whole array of flashmessages.
     *
     * If unset is true, returns the flashmessages and removes them from the session.
     *
     * @return array Flashmessages array
     */
    public static function getMessages($type = null, $unset = true)
    {
        if (isset($_SESSION['user']['flashmessages'])) {
            self::$flashmessages = $_SESSION['user']['flashmessages'];

            if ($unset === true) {
                unset($_SESSION['user']['flashmessages']);
            }
        }

        if ($type !== null) {
            return self::$flashmessages[$type];
        }

        return self::$flashmessages;
    }

    /**
     * Resets the whole flashmessage array.
     */
    public static function reset()
    {
        self::$flashmessages = [];
    }

    /**
     * Renderer for Flashmessages (Viewhelper).
     *
     * @param $type string Type of flashmessage, will render only messages of this type.
     */
    public static function render($type = null)
    {
        $html          = '';
        $flashmessages = self::getMessages($type, true);

        if ($flashmessages !== null) {
            foreach ($flashmessages as $flashmessage) {
                foreach ($flashmessage as $type => $message) {
                    $html .= '<link rel="stylesheet" type="text/css"';
                    $html .= ' href="' . WWW_ROOT_THEMES_CORE . 'css/error.css" />';
                    $html .= '<div id="flashmessage" class="flashmessage ' . $type . '">' . $message . '</div>';
                }
            }

            self::reset();
        }

        return $html;
    }
}
