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
    public function __construct(\Koch\Config\Config $config)
    {
        $this->config = $config;

        //require_once VENDOR_PATH . 'swiftmailer/swiftmailer/lib/swift_required.php';

        \Swift::init('Koch\Mail\SwiftMailer::swiftmailerLazyConfiguration');

        $this->initalizeMailer();
    }

    /**
     * Configure SwiftMailer
     */
    public static function swiftmailerLazyConfiguration()
    {
        // = $this->config[];
        //Swift_DependencyContainer::getInstance()->...
        //Swift_Preferences::getInstance()->...
    }

    /**
     * Instantiates and configures Swift Mailer
     */
    private function initalizeMailer()
    {
        switch ($this->config['email']['mailmethod']) {
            case 'smtp':
                $transport = \Swift_SmtpTransport::newInstance(
                    $this->config['email']['mailerhost'], // 'smtp.gmail.com'
                    $this->config['email']['mailerport'], // 465
                    $this->config['email']['mailencryption'] // tls or ssl
                );
                //->setUsername('me@ff.com')->setPassword('pass');
                break;
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                break;
            case 'exim':
                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs');
                break;
            case 'qmail':
                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/qmail -bs');
                break;
            case 'postfix':
                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/postfix -bs');
                break;
            case 'mail':
            default:
                $transport = \Swift_MailTransport::newInstance();
        }

        $this->mailer = \Swift_Mailer::newInstance($transport);
    }

    /**
     * This is the sendmail command, it's a shortcut method to swiftmailer
     * Return true or false if successfully
     *
     * @param  string  $to      Recipient email ('email => 'name')
     * @param  string  $from    Sender email ('email' => 'name')
     * @param  string  $subject Message subject (headline)
     * @param  string  $body    Message body ('text, 'text/html')
     * @return boolean true|false
     */
    public function send($to, $from, $subject, $body)
    {
        $message = Swift_Message::newInstance($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body);

      // @todo attachments
      //$attachment = Swift_Attachment::newInstance(file_get_contents('path/logo.png'), 'logo.png');
      //$message->attach($attachment);

      $numMailsSent = $this->mailer->send($message); /*batchSend*/

      return sprintf("Sent %d messages\n", $numMailsSent);
    }

    /**
     * Getter Method for the Swiftmailer Object
     *
     * @return object SwiftMailer
     */
    public function getMailer()
    {
        if ($this->mailer === null) {
            $this->initalizeMailer();
        }

        return $this->mailer;
    }
}
