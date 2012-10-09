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
 * Class provides a compositum for loggers.
 *
 * This class represents a compositum for all registered loggers.
 * Another name for this class would be MultiLog.
 * A new logger object is added with addLogger(), removed with removeLogger().
 * The composition is fired via method writeLog().
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Logger
 */
class Compositum implements LoggerInterface
{
    /**
     * @var array Array constains a object composition of all loggers
     */
    public $loggers = array();

    /**
     * Iterates over all registered loggers and writes the log entry.
     *
     * @param mixed|array|string $data  array('message', 'label', 'priority') or message
     * @param string             $label label
     * @param string             $level priority level (LOG, INFO, WARNING, ERROR...)
     */
    public function writeLog($data_or_msg, $label = null, $level = null)
    {
        $data = array();

        // first parameter might be an array or an string
        if (is_array($data_or_msg)) {
            $data = $data_or_msg;
            $data['message'] = $data['0'];
            $data['label'] = $data['1'];
            $data['level'] = $data['2'];
        } else {
            // first parameter is string
            $data['message'] = $data_or_msg;
            $data['label'] = $label;
            $data['level'] = $level;
        }

        foreach ($this->loggers as $logger) {
            $logger->writeLog($data);
        }
    }

    /**
     * Registers new logger(s) as composite element(s)
     *
     * @param array $logger One or several loggers to add
     */
    public function addLogger($loggers)
    {
        // loggers might be an object, so it's typecasted to array, because of foreach
        $loggers = array($loggers);

        foreach ($loggers as $logger) {
            if ((in_array($logger, $this->loggers) == false) and ($logger instanceof LoggerInterface)) {
                $this->loggers[] = $logger;
            }
        }
    }

    /**
     * Removes logger(s) from the compositum
     *
     * @param array $logger One or several loggers to remove
     */
    public function removeLogger($loggers)
    {
        foreach ($loggers as $logger) {
            if ((in_array($logger, $this->loggers) == true)) {
                unset($this->loggers[$logger]);

                return true;
            } else {
                return false;
            }
        }
    }
}
