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
 * Event for Blocking IPs.
 *
 * Usage:
 * $blockip = new BlockIps(array('127.0.0.1'));
 * $dispatcher->addEventHandler('onLogin', $blockip);
 * if ($event->isCancelled()) { }
 *
 */
class BlockIps implements EventInterface
{
    protected $blockedIps;

    public function __construct($blockedIps)
    {
        $this->blockedIps = $blockedIps;
    }

    public function execute(Koch_Event $event)
    {
        $request = Clansuite_CMS::getInjector()->instantiate('Koch_HttpRequest');

        $ip = $request->getRemoteAddress();

        if (in_array($ip,$this->blockedIps)) {
            $event->cancel();
        }
    }
}
