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
 * Log to File.
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
     *
     * @return $filename string
     */
    public function getErrorLogFilename()
    {
        return ROOT_LOGS . 'error_log_' . date('m-d-y') . '.txt';
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
