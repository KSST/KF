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
 * Helper Object for echoing the HTML content onApplicationShutdown
 */
class DebugConsoleResponse implements EventInterface
{
    public $name = 'DebugConsoleResponse';

    private $debugbarHTML;

    public function __construct($debugbarHTML)
    {
        $this->debugbarHTML = $debugbarHTML;
    }

    public function execute()
    {
        echo $this->debugbarHTML;
    }
}
