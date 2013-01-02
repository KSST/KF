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
            $length = strlen($line);
            $newline = "";
            for ($i = 0; $i < $length; $i++) {
                $c = substr($line, $i, 1);
                $dec = ord($c);
                if (($dec == 32) && ($i == ($length - 1))) { // convert space at eol only
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
        for ($i = 0, $items = count($this->items); $i < $items; $i++) {

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
