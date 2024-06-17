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
 * Event for Blocking IPs.
 *
 * Usage:
 * $blockip = new BlockIps(array('127.0.0.1'));
 * $dispatcher->addEventHandler('onLogin', $blockip);
 * if ($event->isCancelled()) { }
 */
class BlockIps implements EventInterface
{
    public function __construct(protected $blockedIps)
    {
    }

    public function execute(\Koch\Event\Event $event)
    {
        $ip = \Koch\Http\HttpRequest::getRemoteAddress();

        if (in_array($ip, $this->blockedIps, true)) {
            $event->cancel();
        }
    }
}
