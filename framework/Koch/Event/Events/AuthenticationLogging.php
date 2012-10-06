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

namespace Koch\Event\Events;

use Koch\Event\EventInterface;

/**
 * Event for Authentication Logging.
 *
 * Usage:
 * $logger = new Koch_Logger('auth.log');
 * $eventhandler->addEventHandler('onInvalidLogin', $logger);
 * $eventhandler->addEventHandler('onLogin', $logger);
 */
class AuthenticationLogging implements EventInterface
{
    protected $logger;

    public function __construct(Koch_Logger $logger, Koch_HttpRequest $request)
    {
        // set request object
        $this->request = $request;
        // set logger object
        $this->logger = $logger;
    }

    public function execute(Koch_Event $event)
    {
        $authdata = $event->getInfo();

        $logdata = array(
                date(),                              // date
                $this->request->getRemoteAddress(),  // remote adress
                $event->getName(),                   // onLogin etc.
                $authdata['username']                // username
        );

        $this->logger->log($logdata);
    }
}
