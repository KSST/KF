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

use Koch\Logger\LoggerInterface;

/**
 * Log to EMail.
 *
 * This class is a service wrapper for sending logging messages via email.
 * The email is send using the Koch_Mailer, which is a wrapper for SwiftMailer.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Logger
 */
class Email implements LoggerInterface
{
    /**
     * @var \Koch\Config\Config
     */
    private $config;

    /**
     * @var \Koch\Mail\SwiftMailer
     */
    private $mailer = null;

    public function __construct(\Koch\Config\Config $config)
    {
        $this->config = $config;

        // mailing of critical errors makes only sense, if we have a email of the sysadmin
        if ($config['mail']['to_sysadmin'] == true) {
            $this->mailer = new \Koch\Mail\SwiftMailer($config);
        }
    }

    /**
     * writeLog - Sends an Email with the message.
     *
     * @param array $data array('message', 'label', 'priority')
     */
    public function writeLog($data)
    {
        $to_address   = $this->config['mail']['to_sysadmin'];
        $from_address = $this->config['mail']['from'];
        // append date/time to msg
        $subject      = '[' . date(DATE_FORMAT, mktime()) . '] ' . $data['label'];
        $body         = var_export($data);

        $this->mailer->send($to_address, $from_address, $subject, $body);
    }
}
