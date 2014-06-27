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

namespace Koch\Event;

/**
 * Interface for all Events.
 *
 * Events have to implement at least a execute() method
 */
interface EventInterface
{
    /**
     * @return void
     */
    public function execute(\Koch\Event\Event $event);
}
