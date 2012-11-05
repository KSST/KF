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
 *
 */

namespace Koch\Logger\Adapter;

use Koch\Logger\LoggerInterface;

/**
 * Koch Framework - Log to File.
 *
 * This class is a service wrapper for logging messages to a logfile.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Logger
 */
class File implements LoggerInterface
{
    /**
     * @var \Koch\Config\Config
     */
    private $config;

    public function __construct(\Koch\Config\Config $config)
    {
        $this->config = $config;
    }

    /**
     * writeLog - Writes a string to the logfile.
     *
     * @param $logfile The name of the Logfile to append to.
     * @param $string The string to append to the logfile.
     */
    public function writeLog($string)
    {
        // append string to file
        file_put_contents($this->getErrorLogFilename(), $string, FILE_APPEND & LOCK_EX);
    }

    /**
     * readLog returns the content of a logfile
     *
     * @param $logfile The name of the logfile to read.
     * @return $string Content of the logfile.
     */
    public static function readLog($logfile = null)
    {
        // errorlog filename as set bei ini_set('error_log')
        #$logfile = ini_get('error_log');

        if ($logfile == null) {
            // hardcoded errorlog filename
            $logfile = 'logs/clansuite_errorlog.txt';
        }

        // determine size of file
        $logfilesize = filesize($logfile);

        // size greater zero, means we have entries in that file
        if ($logfilesize > 0) {
            // so open and read till eof
            $logfile = fopen($logfile, 'r');
            $logfile_content = fread($logfile, $logfilesize);

            // @todo: split or explode logfile_content into an array
            // to select a certain number of entries to display

            // returns the complete logfile
            #return printf("<pre>%s</pre>", $logfile_content);

            return $logfile_content;
        }
    }

    /**
     * This method gives back the filename for logging
     * If rotation is active it will add a date, if seperation is active it will
     *
     * @return $filename string
     */
    public function getErrorLogFilename()
    {
        $file = ROOT_LOGS . 'error';

        // if rotation is active we add a date to the filename
        if ($this->config['log']['rotation'] == true) {
            // construct name of the log file ( FILENAME_log_DATE.txt )
            $file =  $file . '_log_' . date('m-d-y') . '.txt';
        } else {
            // construct name of the log file ( FILENAME_log.txt )
            $file = $file . '_log.txt';
        }

        return $file;
    }

    /**
     * Returns a specific number of logfile entries (last ones first)
     *
     * @param  int    $entriesToFetch
     * @param  string $logfile
     * @return string HTML representation of logfile entries
     */
    public static function getEntriesFromLogfile($entriesToFetch = 5, $logfile = null)
    {
        // setup default logfilename
        if ($logfile == null) {
            $logfile = ROOT_LOGS . 'clansuite_errorlog.txt.php';
        }

         $entries = '';

        if (true === is_file($logfile)) {
            // get logfile as array
            $logfileArray = file($logfile);
            $logfile_cnt = count($logfileArray);

            if ($logfile_cnt > 0) {
                // count array elements = total number of logfile entries
                $i = $logfile_cnt - 1;

                // subtract from total number of logfile entries the number to fetch
                $maxEntries = max(0, $i - $entriesToFetch);

                // reverse for loop over the logfile_array
                for ($i; $i > $maxEntries; $i--) {
                    // remove linebreaks
                    $entry = str_replace(array('\r', '\n'), '', $logfileArray[$i]);

                    $entries .= '<b>Entry ' . $i . '</b>';
                    $entries .= '<br />' . htmlentities($entry) . '<br />';
                }

                // cleanup
                unset($logfile, $logfileArray, $i, $maxEntries, $entry);
            } else {
                $entries .= '<b>No Entries</b>';
            }
        } else {
            $entries .= '<b>No Logfile found. No entries yet.</b>';
        }

        return $entries;
    }
}
