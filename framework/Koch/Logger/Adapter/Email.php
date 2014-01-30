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

namespace Koch\Logger\Adapter;

use Koch\Logger\AbstractLogger;
use Koch\Logger\LoggerInterface;

/**
 * Log to EMail.
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
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
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
