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
 */

namespace Koch\Logger\Adapter;

use Koch\Logger\AbstractLogger;
use Koch\Logger\LoggerInterface;

/**
 * Koch Framework - Log to EMail.
 *
 * This class is a service wrapper for sending logging messages via email.
 * The email is send using the \Koch\Mailer\Mailer, which is a wrapper for SwiftMailer.
 */
class Email extends AbstractLogger implements LoggerInterface
{
    /**
     * @var array Options.
     */
    private $options = array();

    /**
     * @var \Koch\Mail\SwiftMailer
     */
    private $mailer = null;

    public function __construct($config)
    {
        $this->setOptions($config);

        // mailing of critical errors makes only sense, if we have a email of the sysadmin
        if (true === (bool) $this->options['to_sysadmin']) {
            $this->mailer = new \Koch\Mail\SwiftMailer($config);
        }
    }

    public function setOptions($options)
    {
        // assign "mail" subarray
        $this->options = $options['mail'];
    }

    /**
     * Sends the log message via E-Mail.
     *
     * @param  string  $level
     * @param  string $message
     * @param  string[]  $context
     * @return bool
     */
    public function log($level, $message, array $context = array())
    {
        $to_address   = $this->options['to_sysadmin'];
        $from_address = $this->options['from'];

        // append date/time to message
        $subject      = '[' . date(DATE_RFC2822, time()) . '] ' . $message;
        $body         = var_export($message, true);

        return (bool) $this->mailer->send($to_address, $from_address, $subject, $body);
    }
}
