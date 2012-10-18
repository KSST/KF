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

namespace Koch\Feed\Generator;

use Koch\Feed\Generator;

/**
 * MBOXCreator is a FeedCreator that implements the mbox format
 * as described in http://www.qmail.org/man/man5/mbox.html
 */
class MBox extends Generator
{

    /**
     * The file extension to be used in the cache file
     */
    protected $suffix = 'mbox';

    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        
        $this->contentType = "text/plain";
        $this->encoding = "ISO-8859-15";
    }

    public static function qpEnc($input = "", $line_max = 76)
    {
        $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $lines = preg_split("/(?:\r\n|\r|\n)/", $input);
        $eol = "\r\n";
        $escape = "=";
        $output = "";
        while (list(, $line) = each($lines)) {
            //$line = rtrim($line); // remove trailing white space -> no =20\r\n necessary
            $linlen = strlen($line);
            $newline = "";
            for ($i = 0; $i < $linlen; $i++) {
                $c = substr($line, $i, 1);
                $dec = ord($c);
                if (($dec == 32) && ($i == ($linlen - 1))) { // convert space at eol only
                    $c = "=20";
                } elseif (($dec == 61) || ($dec < 32 ) || ($dec > 126)) { // always encode "\t", which is *not* required
                    $h2 = floor($dec / 16);
                    $h1 = floor($dec % 16);
                    $c = $escape . $hex["$h2"] . $hex["$h1"];
                }
                if ((strlen($newline) + strlen($c)) >= $line_max) { // CRLF is not counted
                    $output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
                    $newline = "";
                }
                $newline .= $c;
            } // end of for
            $output .= $newline . $eol;
        }

        return trim($output);
    }

    /**
     * Builds the MBOX contents.
     * @return string the feed's complete text
     */
    public function renderFeed()
    {
        for ($i = 0; $i < count($this->items); $i++) {

            if ($this->items[$i]->author != "") {
                $from = $this->items[$i]->author;
            } else {
                $from = $this->title;
            }

            $itemDate = new FeedDate($this->items[$i]->date);

            $feed .= "From " . strtr(self::qpEnc($from), " ", "_") . " ";
            $feed .= date("D M d H:i:s Y", $itemDate->unix()) . "\n";
            $feed .= "Content-Type: text/plain;\n";
            $feed .= "	charset=\"" . $this->encoding . "\"\n";
            $feed .= "Content-Transfer-Encoding: quoted-printable\n";
            $feed .= "Content-Type: text/plain\n";
            $feed .= "From: \"" . self::qpEnc($from) . "\"\n";
            $feed .= "Date: " . $itemDate->rfc822() . "\n";
            $feed .= "Subject: " . self::qpEnc(FeedCreator::iTrunc($this->items[$i]->title, 100)) . "\n";
            $feed .= "\n";

            $body = chunk_split(self::qpEnc($this->items[$i]->description));

            $feed .= preg_replace("~\nFrom ([^\n]*)(\n?)~", "\n>From $1$2\n", $body);
            $feed .= "\n";
            $feed .= "\n";
        }

        return $feed;
    }

    /**
     * Generate a filename for the feed cache file. Overridden from FeedCreator to prevent XML data types.
     *
     * @return string the feed cache filename
     */
    protected function generateFilename()
    {
        $fileInfo = pathinfo($_SERVER['PHP_SELF']);

        return substr($fileInfo["basename"], 0, -(strlen($fileInfo["extension"]) + 1)) . ".mbox";
    }
}
