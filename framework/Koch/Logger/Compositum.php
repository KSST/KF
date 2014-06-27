<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Logger;

/**
 * Koch Framework - Class provides a compositum for loggers.
 *
 * This class represents a compositum for all registered loggers.
 * Another name for this class would be MultiLogger.
 * A new logger object is added with addLogger(), removed with removeLogger().
 * The composition is fired via method log().
 */
class Compositum
{
    /**
     * @var array Array constains a object composition of all loggers
     */
    public $loggers = array();

    /**
     * Iterates over all registered loggers and writes the log entry.
     *
     * @param string $level   priority level (LOG, INFO, WARNING, ERROR...)
     * @param string $message
     * @param string[]  $context Context Array
     */
    public function log($level, $message, array $context = array())
    {
        $bool = true;

        foreach ($this->loggers as $logger) {
            $bool = $bool && $logger->log($level, $message, $context);
        }

        // combined boolean return value
        return $bool;
    }

    /**
     * Registers a logger as composite element.
     *
     * @param array $logger Logger to add
     */
    public function addLogger($logger)
    {
        if ((in_array($logger, $this->loggers) === false)) {
            $this->loggers[] = $logger;
        }

        return true;
    }

    /**
     * Remove a logger from the compositum.
     *
     * @param string $logger Logger to remove
     */
    public function removeLogger($logger)
    {
        $logger = 'Koch\Logger\Adapter\\' . ucfirst($logger);

        foreach ($this->loggers as $key => $compositeLogger) {
            // compare classnames
            if (get_class($compositeLogger) === $logger) {
                unset($this->loggers[$key]);
            }
        }

        return false;
    }
}
