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

namespace Koch\Event\Events;

use Koch\Event\EventInterface;

/**
 * Event for Authentication Logging.
 *
 * Usage:
 * $logger = new \Koch\Logger\Logger('auth.log');
 * $eventhandler->addEventHandler('onInvalidLogin', $logger);
 * $eventhandler->addEventHandler('onLogin', $logger);
 */
class AuthenticationLogging implements EventInterface
{
    protected $logger;

    public function __construct(\Koch\Logger\LoggerInterface $logger, \Koch\Http\HttpRequestInterface $request)
    {
        // set request object
        $this->request = $request;
        // set logger object
        $this->logger = $logger;
    }

    public function execute(\Koch\Event\Event $event)
    {
        $authdata = $event->getInfo();

        $logdata = [
                date(),                              // date
                $this->request->getRemoteAddress(),  // remote adress
                $event->getName(),                   // onLogin etc.
                $authdata['username'],                // username
        ];

        $this->logger->log($logdata);
    }
}
