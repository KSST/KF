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

namespace Koch\Http;

/**
 * Koch Framework - Response Encode
 *
 * The class is used to gzip_encode the response (php output).
 *
 * Usage:
 * 1. Include/require/autoload the class file (Koch_ResponseEncode).
 * 2. Start Output buffering by calling
 *    self::start_outputbuffering();
 * 3. At the very end of your script you have to end the outputbuffering by calling
 *    self::end_outputbuffering();
 *
 * Example:
 *  ------------Start of file----------
 *  |<?php
 *  | require 'responseencode.core.php'; // or autoload
 *  | self::start_outputbuffering();
 *  |?>
 *  |<html>
 *  |... Content of Page ...
 *  |</html>
 *  |<?php
 *  | self::end_outputbuffering();
 *  |?>
 *  -------------End of file-----------
 *
 * Resources:
 *  http://www.ietf.org/rfc/rfc2616.txt (Sections: 3.5, 14.3, 14.11)
 *  http://www.whatsmyip.org/http_compression/
 *
 * Requirments: PHP5 & PHP Extensions: zlib, crc
 *
 * Note:
 *  TYPO3 4.5 is now using "ob_gzhandler" for compression.
 *  That is suboptimal because using zlib.output_compression is preferred over ob_gzhandler().
 */
class ResponseEncode
{
    /**
     * Integer holding the GZIP compression level
     * Default = -1 compressionlevel of system; normal range 0-9
     * @var      integer
     */
    protected static $compression_level = 7;

    public static function startOutputBuffering()
    {
        // both methods depend on the zlib extension
        if (extension_loaded('zlib')) {
            if((bool) ini_get('zlib.output_compression') === false
               and (ini_get('output_handler') != 'ob_gzhandler') and ob_get_length() === false) {
                // Method 1: on-the-fly transparent zlib.output_compression
                // Additional output handlers are not valid, when zlib.output_compression is activated.
                #ini_set('zlib.output_compression'       , true);
                #ini_set('zlib.output_compression_level' , self::$compression_level);

                // if zlib.output_compression still not enabled
                // Method 2: compression via this class
                if ((bool) ini_get('zlib.output_compression') === false) {
                    ob_start();
                    ob_implicit_flush(0);
                }
            } else {
                // Method 3: Fallback to ob_start('gz_handler') = output buffering with gzip handling
                // because: output handler 'ob_gzhandler' conflicts with 'zlib output compression'
                ob_start('ob_gzhandler');
            }
        } else {
            trigger_error(
                'Function gzcompress() not found. PHP Extension Zlib needs to be installed for ResponseEncode.',
                E_USER_WARNING
            );

            return;
        }

        if (function_exists('crc32') == false) {
            trigger_error('Function crc32() not found. Needed for ResponseEncode', E_USER_WARNING);

            return;
        }
    }

    /**
     * Convenience/Proxy method for gzip_encode
     */
    public static function stopOutputBuffering()
    {
        self::gzipEncode();
    }

    /**
     * gzip_encode
     * Purpose: gzip encodes the current output buffer, if the browser supports it.
     *
     * This method takes care of the output compression.
     * Because you cannot use both ob_gzhandler() and zlib.output_compression.
     * Therefore zlib.output_compression is preferred over ob_gzhandler().
     * So two methods are used:
     * Method 1: zlib.output_compression
     * Method 2: Fallback to ob_start('gz_handler') = output buffering with gzip handling
     * The way this method works is determined by start_encoding!
     * @see start_encoding();
     *
     * You can specify one of the following for the first argument:
     *  0:      No compression
     *  1:      Min compression
     *  1-9:    Range of compression levels
     *  9:      Max compression
     *  true:   Determin the compression level from the system load.
     *          The higher the load the less the compression.
     *
     * @param int level
     * @param boolean debug
     * @param int outputCompressedSizes
     */
    public static function gzipEncode()
    {
        if (headers_sent()) {
            return;
        }

        if (connection_status() !== 0) {
            return;
        }

        $encoding = self::gzipAccepted();

        if ($encoding == false) {
            return;
        }

        $level = self::$compression_level;

        $content = ob_get_contents();

        // if no content is given, exit
        if ($content === false) {
            return;
        }

        // determine the content size
        $original_content_size = mb_strlen($content);

        // if size of content is small, do not waste resources in compressing very little data, exit
        if ($original_content_size < 2048) {
            return;
        }

        /**
         * The Content Compression
         */
        switch ($encoding) {
            default:
            case 'compress':
            case 'gzip':
                // gzip header
                $gzdata = '\x1f\x8b\x08\x00\x00\x00\x00\x00';

                // compress
                $gzdata .= gzcompress($content, $level);

                // determine size of compressed content
                $compressed_content_size = mb_strlen($gzdata);

                // fix crc bug
                $gzdata = mb_substr($gzdata, 0, $compressed_content_size - 4);

                // add pack infos
                $gzdata .= pack('V', crc32($content)) . pack('V', $original_content_size);

                break;
            case 'x-gzip':
                $gzdata = gzencode($content, $level);
                break;
            case 'deflate':
                $gzdata = gzdeflate($content, $level);
                break;
        }

        // delete output-buffer and deactivate buffering
        ob_end_clean();

        // send Headers
        header('Content-Encoding: ' . $encoding);
        header('Vary: Accept-Encoding');
        header('Content-Length: ' . (int) mb_strlen($gzdata));

        /**
         * Note by Jens-Andrï¿½ Koch:
         *
         * The Content Compression Info Comment was originally added by Kasper Skaarhoj for Typo3.
         * This had the problem of wasting resources by using gzcompress two times.
         * One time to determine the compressed_content_size and a second time for the
         * compression of the content (gzdata). This gets rid of the double gzcompression usage.
         * The compression info message is now passed via header to the client.
         */
        // calculate compression ratio
        $compression_ratio = round((100 / $original_content_size) * $compressed_content_size);

        // construct Content Compression Info Comment
        $msg = 'Compression Level ' . $level
            . '. Ratio ' . $compression_ratio
            . '%. Original size was ' . $original_content_size
            . ' bytes. New size is ' . $compressed_content_size . ' bytes.';

        // set compression-info header
        header('X-Content-Compression-Info: ' . $msg);

        // FLUSH THE COMPRESSED CONTENT
        echo $gzdata;
    }

    /**
     * gzip_accepted()
     * Purpose: test headers for Accept-Encoding: gzip/x-gzip
     *
     * Usage to test if output will be zipped:
     * if (self::gzip_accepted()) { echo "Page will be gziped"; }
     *
     * @return mixed (string|boolean) $encoding Returns 'gzip' or 'x-gzip' if
     *               Accept-Encoding Header is found. False otherwise.
     */
    public static function gzipAccepted()
    {
        // init vars
        $encoding = null;
        $http_accept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];

        // check Accept-Encoding for x-gzip
        if (mb_strpos($http_accept_encoding, 'x-gzip') !== false) {
            $encoding = 'x-gzip';
        }

        // check Accept-Encoding for gzip
        if (mb_strpos($http_accept_encoding, 'gzip') !== false) {
            $encoding = 'gzip';
        }

        // Perform a "qvalue" check. The Accept-Encoding "gzip;q=0" means that gzip is NOT accepted.
        // preg_matches only, if first condition is true.
        if ((mb_strpos($http_accept_encoding, 'gzip;q=') !== false)
            and (preg_match('/(^|,\s*)(x-)?gzip(;q=(\d(\.\d+)?))?(,|$)/i', $http_accept_encoding, $match)
            and ($match[4] === '' or $match[4] > 0))) {
            $encoding = 'gzip';
        }

        /**
         * Determine file type by checking the first bytes of the content buffer.
         */
        $magic = mb_substr(ob_get_contents(), 0, 4);
        if (mb_substr($magic, 0, 2) === '^_') {
            // gzip data
            $encoding = false;
        } elseif (mb_substr($magic, 0, 3) === 'GIF') {
            // gif images
            $encoding = false;
        } elseif (mb_substr($magic, 0, 2) === "\xFF\xD8") {
            // jpeg images
            $encoding = false;
        } elseif (mb_substr($magic, 0, 4) === "\x89PNG") {
            // png images
            $encoding = false;
        } elseif (mb_substr($magic, 0, 3) === 'FWS') {
            // Don't gzip Shockwave Flash files.
            // Flash on windows incorrectly claims it accepts gzip'd content.
            $encoding = false;
        } elseif (mb_substr($magic, 0, 2) === 'PK') {
            // pk zip file
            $encoding = false;
        }

        return $encoding;
    }
}
