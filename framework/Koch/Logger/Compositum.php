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
     * @param mixed|array|string $data  array(message, label, level) or message
     * @param string             $label label
     * @param string             $level priority level (LOG, INFO, WARNING, ERROR...)
     */
    public function writeLog($data_or_msg, $label = null, $level = null)
    {
        $data = array();

        if (is_array($data_or_msg) === true) {
            // first parameter is array
            $data['message'] = $data_or_msg[0];
            $data['label'] = $data_or_msg[1];
            $data['level'] = $data_or_msg[2];
        } else {
            // first parameter is string
            $data['message'] = $data_or_msg;
            $data['label'] = $label;
            $data['level'] = $level;
        }

        // combined boolean return value
        $bool = true;
        foreach ($this->loggers as $logger) {
            $bool = $bool && $logger->writeLog($data);
        }
        return $bool;
    }

    /**
     * Registers a logger as composite element
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
     * Remove a logger from the compositum
     *
     * @param array $logger Logger to remove
     */
    public function removeLogger($logger)
    {
        $logger = 'Koch\Logger\Adapter\\' . ucfirst($logger);

        foreach($this->loggers as $compositeLogger)
        {
            if($compositeLogger instanceof $logger) {
                unset($this->loggers[$logger]);
            }
        }

        return false;
    }
}
