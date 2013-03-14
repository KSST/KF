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

namespace Koch\Logger;

/**
 * Interface for the Logger Adapters.
 */
interface LoggerInterface
{
    // each logger has to provide the method log()
    public function log($data);
}
