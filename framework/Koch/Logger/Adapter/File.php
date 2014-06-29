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

namespace Koch\Logger\Adapter;

use Koch\Logger\AbstractLogger;
use Koch\Logger\LoggerInterface;

/**
 * Koch Framework - Log to File.
 *
 * This class is a service wrapper for logging messages to a logfile.
 */
class File extends AbstractLogger implements LoggerInterface
{
    private $logfile;

    /**
     * This method gives back the filename for logging
     *
     * @return $filename string
     */
    public function getErrorLogFilename()
    {
        $file = APPLICATION_PATH . 'logs/errorlog-' . date('dmY') . '.txt';

        return (empty($this->logfile)) ? $file : $this->logfile;
    }

    /**
     * @param string $file
     */
    public function setErrorLogFilename($file)
    {
        $this->logfile = $file;
    }

    /**
     * Writes a string to the logfile.
     *
     * @param  string $level
     * @param  string $message
     * @param  array  $context
     * @return bool
     */
    public function log($level, $message, array $context = array())
    {
        return (bool) file_put_contents($this->getErrorLogFilename(), $message, FILE_APPEND & LOCK_EX);
    }

    /**
     * readLog returns the content of a logfile
     *
     * @param $logfile The name of the logfile to read.
     * @return $string Content of the logfile.
     */
    public function readLog($logfile = null)
    {
        if ($logfile == null) {
            // hardcoded errorlog filename
            $logfile = $this->getErrorLogFilename();
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
     * Returns a specific number of logfile entries (last ones first)
     *
     * @param  int    $entriesToFetch
     * @param  string $logfile
     * @return string HTML representation of logfile entries
     */
    public function getEntriesFromLogfile($entriesToFetch = 5, $logfile = null)
    {
        // setup default logfilename
        if ($logfile === null) {
            $logfile = $this->getErrorLogFilename();
        }

         $entries = '';

        if (true === is_file($logfile)) {
            // get logfile as array
            $logfileArray = file($logfile);
            $logfile_cnt = count($logfileArray);

            if ($logfile_cnt > 0) {
                // subtract from total number of logfile entries the number to fetch
                $maxEntries = max(1, $logfile_cnt - $entriesToFetch);
                // count array elements = total number of logfile entries
                $i = $logfile_cnt - 1;

                // reverse for loop over the logfile_array to get the last few (new ones) log entries
                for ($i; $i >= $maxEntries; $i--) {
                    // remove linebreaks
                    $entry = str_replace(array('\r', '\n'), '', $logfileArray[$i]);
                    $entry = htmlentities($entry);

                    $tpl = '<span class="log-id">Entry %s</span><span class="log-entry">%s</span>' . "\n";
                    $entries .= sprintf($tpl, $i+1, $entry);
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
