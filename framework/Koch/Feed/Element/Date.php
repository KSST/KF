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

namespace Koch\Feed\Element;

/**
 * FeedDate is an internal class that stores a date for a feed or feed item.
 */
class Date
{

    public $unix;

    /**
     * Creates a new instance of FeedDate representing a given date.
     * Accepts RFC 822, ISO 8601 date formats as well as unix time stamps.
     *
     * @param mixed $dateString optional the date this FeedDate will represent.
     *                          If not specified, the current date and time is used.
     */
    public function __construct($dateString = '')
    {
        if ($dateString == '') {
            $dateString = date('r');
        }

        if (is_numeric($dateString)) {
            $this->unix = $dateString;

            return;
        }

        $regexp = "~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)"
            . "?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~";

        $matches = array();

        if (preg_match($regexp, $dateString, $matches)) {
            $months = array(
                'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
                'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
            );
            $this->unix = mktime($matches[4], $matches[5], $matches[6], $months[$matches[2]], $matches[1], $matches[3]);
            if (substr($matches[7], 0, 1) == '+' OR substr($matches[7], 0, 1) == '-') {
                $tzOffset = (substr($matches[7], 0, 3) * 60 + substr($matches[7], -2)) * 60;
            } else {
                if (strlen($matches[7]) == 1) {
                    $oneHour = 3600;
                    $ord = ord($matches[7]);
                    if ($ord < ord("M")) {
                        $tzOffset = (ord("A") - $ord - 1) * $oneHour;
                    } elseif ($ord >= ord("M") AND $matches[7] != "Z") {
                        $tzOffset = ($ord - ord("M")) * $oneHour;
                    } elseif ($matches[7] == "Z") {
                        $tzOffset = 0;
                    }
                }
                switch ($matches[7]) {
                    case 'UT':
                    case 'GMT': $tzOffset = 0;
                }
            }
            $this->unix += $tzOffset;

            return;
        }
        if (preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~", $dateString, $matches)) {
            $this->unix = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
            if (substr($matches[7], 0, 1) == '+' OR substr($matches[7], 0, 1) == '-') {
                $tzOffset = (substr($matches[7], 0, 3) * 60 + substr($matches[7], -2)) * 60;
            } else {
                if ($matches[7] == 'Z') {
                    $tzOffset = 0;
                }
            }
            $this->unix += $tzOffset;

            return;
        }
        $this->unix = 0;
    }

    /**
     * Gets the date stored in this FeedDate as an RFC 822 date.
     *
     * @return a date in RFC 822 format
     */
    public function rfc822()
    {
        //return gmdate("r",$this->unix);
        $date = gmdate("D, d M Y H:i:s", $this->unix);
        if (TIME_ZONE != '') {
            $date .= ' ' . str_replace(':', '', TIME_ZONE);
        }

        return $date;
    }

    /**
     * Gets the date stored in this FeedDate as an ISO 8601 date.
     *
     * @return a date in ISO 8601 (RFC 3339) format
     */
    public function iso8601()
    {
        $date = gmdate("Y-m-d\TH:i:sO", $this->unix);
        $date = substr($date, 0, 22) . ':' . substr($date, -2);
        if (TIME_ZONE != '') {
            $date = str_replace("+00:00", TIME_ZONE, $date);
        }

        return $date;
    }

    /**
     * Gets the date stored in this FeedDate as unix time stamp.
     *
     * @return a date as a unix time stamp
     */
    public function unix()
    {
        return $this->unix;
    }
}
