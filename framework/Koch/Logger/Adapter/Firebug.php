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
 * Log to Firebug.
 *
 * This class is a service wrapper for logging messages to the firebug browser console
 * via the famous FirePHP Firefox extension.
 * In one sentence: Forget about echo, var_dump, print_r for debugging purposes.
 *
 * FirePHP is a Firebug Extension for AJAX Development written by Christoph Dorn.
 * With simple PHP method calls one is able to log to the Firebug Console.
 * The data does not interfere with the page content as it is sent via  X-FirePHP-Data response headers.
 * That makes FirePHP the ideal tool for AJAX development where clean JSON and XML responses are required.
 * Firebug is written by Joe Hewitt and Rob Campbell.
 *
 * @link http://getfirebug.com/
 * @link http://firephp.org/
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Logger
 */
class Firebug implements LoggerInterface
{
    private static $firephp = null;

    public function __construct()
    {
        if (self::$firephp === null) {
            include __DIR__ . '/../../../../vendor/firephp/FirePHP.class.php';

            self::$firephp = \FirePHP::getInstance(true);
        }

        return self::$firephp;
    }

    /**
     * geFirePHPLoglevel
     * translates the system errorlevel to the loglevel known by firephp
     *
     * @param string $level (comming from $data['level'] of the $data array to log)
     */
    public function getFirePHPLoglevel($level)
    {
        switch (strtoupper($level)) {
            case 'LOG':
                return FirePHP::LOG;
            case 'INFO':
                return FirePHP::INFO;
            case 'WARNING':
                return FirePHP::WARN;
            case 'ERROR':
                return FirePHP::ERROR;
            case 'NOTICE':
                return FirePHP::NOTICE;
            case 'DEBUG':
                return FirePHP::DEBUG;
            case 'TABLE':
                return FirePHP::TABLE;
            case 'TRACE':
                return FirePHP::TRACE; // backtracing
            case 'DUMP':
                return FirePHP::DUMP; // variable dumps
            default:
                return FirePHP::ERROR;
        }
    }

    /**
     * This writes a log to the Firephp or Firebug console.
     *
     * It utilizes firephp's procedural API.
     * fb($var, 'Label', FirePHP::*)
     *
     * @param $data array date['message'], data['label'], data['level']
     */
    public function writeLog($data)
    {
        self::$firephp->fb($data['message'], $data['label'], $this->getFirePHPLoglevel($data['level']));
    }
}
