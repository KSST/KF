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
 * Helper Object for echoing the HTML content onApplicationShutdown.
 */
class DebugConsoleResponse implements EventInterface
{
    public $name = 'DebugConsoleResponse';

    public function __construct(private $debugbarHTML)
    {
    }

    public function execute(\Koch\Event\Event $event)
    {
        echo $this->debugbarHTML;
    }
}
