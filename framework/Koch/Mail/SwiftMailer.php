<?php

/**
 * Koch Framework
 * Jens-Andrï¿½ Koch ï¿½ 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Mail;

/**
 * Class for Mail Handling with SwiftMailer.
 *
 * This is a simple wrapper for SwiftMailer.
 * @link http://swiftmailer.org/
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Mailer
 */
class SwiftMailer
{
    public $mailer = null;

    private $config = null;

    /**
     * Constructor.
     */
    public function __construct(Koch\Config $config)
    {
        $this->config = $config;
        $this->loadMailer();
    }

    /**
     * Loads and instantiates Swift Mailer
     */
    private function loadMailer()
    {
        $vendor = __DIR__ . '/../../vendor/';

        // Include the Swiftmailer Class
        include $vendor . 'swiftmailer/Swift.php';

        /**
         * Include the Swiftmailer Connection Class and Set $connection
         */
        if ($this->config['email']['mailmethod'] != 'smtp') {
            include $vendor . 'swiftmailer/Swift/Connection/Sendmail.php';
        }

        switch ($this->config['email']['mailmethod']) {
            case 'smtp':
                include $vendor . 'swiftmailer/Swift/Connection/SMTP.php';
                $connection = new Swift_Connection_SMTP(
                    $this->config['email']['mailerhost'], 
                    $this->config['email']['mailerport'], 
                    $this->config['email']['mailencryption']
                );
                break;
            case 'sendmail':
                $connection = new Swift_Connection_Sendmail;
                break;
            case 'exim':
                $connection = new Swift_Connection_Sendmail('/usr/sbin/exim -bs');
                break;
            case 'qmail':
                $connection = new Swift_Connection_Sendmail('/usr/sbin/qmail -bs');
                break;
            case 'postfix':
                $connection = new Swift_Connection_Sendmail('/usr/sbin/postfix -bs');
                break;
            default:
                $connection = new Swift_Connection_Sendmail;
        }

        //  This globalizes $this->mailer and initialize the class
        $this->mailer = new Swift($connection, $this->config['email']['mailerhost']);
    }

    /**
     * This is the sendmail command, it's a shortcut method to swiftmailer
     * Return true or false if successfully
     *
     * @param  string  $to      Recipient email
     * @param  string  $from    Sender email
     * @param  string  $subject Message subject (headline)
     * @param  string  $body    Message body
     * @return boolean true|false
     */
    public function send($to, $from, $subject, $body)
    {
        if ($this->mailer->isConnected()) {
            // sends a simple email via the instantiated mailer
            $this->mailer->send($to, $from, $subject, $body);

            // close mailer
            $this->mailer->close();

            return true;
        } else {
            trigger_error(
                'The mailer failed to connect.
                Errors: <br/>' . '<pre>' . print_r($this->mailer->errors, 1) . '</pre>' . '
                Log: <pre>' . print_r($this->mailer->transactions, 1) . '</pre>',
                E_USER_NOTICE
            );

            return false;
        }
    }

    /**
     * Getter Method for the Swiftmailer Object
     *
     * @return object SwiftMailer
     */
    public function getMailer()
    {
        if ($this->mailer === null) {
            $this->loadMailer();
        }

        return $this->mailer;
    }
}
