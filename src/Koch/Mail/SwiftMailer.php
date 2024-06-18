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

namespace Koch\Mail;

/**
 * Class for Mail Handling with SwiftMailer.
 *
 * This is a simple wrapper for SwiftMailer.
 *
 * @link http://swiftmailer.org/
 * @link http://swiftmailer.org/docs/introduction.html Documentation
 */
class SwiftMailer
{
    /* @var \Swift_Mailer */
    public $mailer;

    /* @var array Options */
    private $options = [];

    /**
     * Constructor.
     */
    public function __construct($options)
    {
        // assign "email" subarray
        $this->options = $options['email'];

        \Swift::init('Koch\Mail\SwiftMailer::swiftmailerLazyConfiguration');

        $this->initializeMailer();
    }

    /**
     * Configure SwiftMailer.
     */
    public static function swiftmailerLazyConfiguration()
    {
        // = $this->config[];
        //Swift_DependencyContainer::getInstance()->...
        //Swift_Preferences::getInstance()->...
    }

    /**
     * Instantiates and configures Swift Mailer.
     */
    private function initializeMailer()
    {
        $transport = match ($this->options['method']) {
            'smtp' => \Swift_SmtpTransport::newInstance(
                $this->options['host'], // 'smtp.gmail.com'
                $this->options['port'], // 465
                $this->options['encryption'] // tls or ssl
            ),
            'sendmail' => \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs'),
            'exim' => \Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs'),
            'qmail' => \Swift_SendmailTransport::newInstance('/usr/sbin/qmail -bs'),
            'postfix' => \Swift_SendmailTransport::newInstance('/usr/sbin/postfix -bs'),
            default => \Swift_MailTransport::newInstance(),
        };

        // Create the Mailer using the created Transport
        $this->mailer = \Swift_Mailer::newInstance($transport);
    }

    /**
     * This is the sendmail command, it's a shortcut method to swiftmailer
     * Return true or false if successfully.
     *
     * @param string $to      Recipient email ('email => 'name')
     * @param string $from    Sender email ('email' => 'name')
     * @param string $subject Message subject (headline)
     * @param string $body    Message body ('text, 'text/html')
     *
     * @return string true|false
     */
    public function send($to, $from, $subject, $body)
    {
        $message = \Swift_Message::newInstance($subject)
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
     * Getter Method for the Swiftmailer Object.
     *
     * @return \Swift_Mailer SwiftMailer
     */
    public function getMailer()
    {
        if ($this->mailer === null) {
            $this->initializeMailer();
        }

        return $this->mailer;
    }
}
