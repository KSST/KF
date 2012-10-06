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

namespace Koch\Logger\Adapter;

use Koch\Logger\LoggerInterface;

/**
 * Log to /dev/null.
 *
 * This class is a service wrapper for logging messages to /dev/null.
 * It's a dummy logger - doing nothing.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Logger
 */
class Devnull implements LoggerInterface
{
    /**
     * writeLog
     *
     * writes a string to /dev/null nirvana.
     *
     * @param $string The string to append to the logfile.
     */
    public function writeLog($string)
    {
        unset($string);
    }
}
