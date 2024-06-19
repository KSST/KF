<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Http;

/**
 * Response Compression.
 *
 * @todo rewrite and take possible external compression into account
 *
 * The class is used to buffer and compress the response content.
 *
 * Because you cannot use both ob_gzhandler() and zlib.output_compression,
 * zlib.output_compression is preferred over ob_gzhandler().
 *
 * So two methods are used:
 * Method 1: zlib.output_compression
 * Method 2: stopBuffer
 * Method 3: ob_start('gz_handler')
 *
 * Resources:
 *
 * @link http://www.ietf.org/rfc/rfc2616.txt (Sections: 3.5, 14.3, 14.11)
 * @link http://www.whatsmyip.org/http_compression/
 *
 * Note:
 *  TYPO3 4.5 is now using "ob_gzhandler" for compression.
 *  That is suboptimal because using zlib.output_compression is preferred over ob_gzhandler().
 */
class ResponseCompression
{
    public static function startBuffer()
    {
        // both methods depend on the zlib extension
        if (extension_loaded('zlib') === false) {
            throw new \Koch\Exception\Exception('The PHP Extension "zlib" is required.');
        }

        $zlib_output_compression = (bool) ini_get('zlib.output_compression');

        if ($zlib_output_compression === false and ob_get_length() === false
            && (ini_get('output_handler') !== 'ob_gzhandler')) {
            // Method 1: on-the-fly transparent zlib.output_compression
            // Additional output handlers are not valid, when zlib.output_compression is activated.
            ini_set('zlib.output_compression', true);
            $zlib_output_compression = true;
            ini_set('zlib.output_compression_level', 7);

            // if zlib.output_compression is still not enabled
            // Method 2: compression via this class
            if ($zlib_output_compression === false) {
                ob_start();
                ob_implicit_flush(0);
            }
        } else {
            // Method 3: Fallback to ob_start('gz_handler') = output buffering with gzip handling
            // because: output handler 'ob_gzhandler' conflicts with 'zlib output compression'
            ob_start('ob_gzhandler');
        }
    }

    /**
     * FLushes the gzip_encoded output buffer, if the browser supports it.
     */
    public static function flushCompressedBuffer()
    {
        if (headers_sent()) {
            return;
        }

        if (connection_status() !== 0) {
            return;
        }

        $encoding = self::gzipAccepted();

        if ($encoding === false) {
            return;
        }

        $level = 7;

        $content = ob_get_contents();

        // exit, if no content is given
        if ($content === false) {
            return;
        }

        // determine the content size
        $original_content_size = mb_strlen($content);

        // do not waste resources in compressing very little data
        if ($original_content_size < 2048) {
            return;
        }

        // the compression
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

        header('Content-Encoding: ' . $encoding);
        header('Vary: Accept-Encoding');
        header('Content-Length: ' . mb_strlen($gzdata));

        /*
         * Note by Jens A. Koch:
         *
         * The Content Compression Info Comment was originally added by Kasper Skaarhoj for Typo3.
         * This had the problem of wasting resources by using gzcompress two times.
         * One time to determine the compressed_content_size and a second time for the
         * compression of the content (gzdata). This gets rid of the double gzcompression usage.
         * The compression info message is now passed via header to the client.
         */
        // calculate compression ratio
        $compression_ratio = round((100 / $original_content_size) * $compressed_content_size);

        // set Content Compression Info Header
        $msg = 'Compression Level ' . $level
            . '. Ratio ' . $compression_ratio
            . '%. Original size was ' . $original_content_size
            . ' bytes. New size is ' . $compressed_content_size . ' bytes.';
        header('X-Content-Compression-Info: ' . $msg);

        // flush compressed content
        echo $gzdata;
    }

    /**
     * gzip_accepted()
     * Purpose: test headers for Accept-Encoding: gzip/x-gzip.
     *
     * Usage to test if output will be zipped:
     * if (self::gzip_accepted()) { echo "Page will be gziped"; }
     *
     * @return false|string (string|boolean) $encoding Returns 'gzip' or 'x-gzip' if
     *                      Accept-Encoding Header is found. False otherwise.
     */
    public static function gzipAccepted()
    {
        // init vars
        $encoding             = '';
        $http_accept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];

        // check Accept-Encoding for x-gzip
        if (mb_strpos((string) $http_accept_encoding, 'x-gzip') !== false) {
            $encoding = 'x-gzip';
        }

        // check Accept-Encoding for gzip
        if (mb_strpos((string) $http_accept_encoding, 'gzip') !== false) {
            $encoding = 'gzip';
        }

        // Perform a "qvalue" check. The Accept-Encoding "gzip;q=0" means that gzip is NOT accepted.
        // preg_matches only, if first condition is true.
        if ((mb_strpos((string) $http_accept_encoding, 'gzip;q=') !== false)
            && (preg_match('/(^|,\s*)(x-)?gzip(;q=(\d(\.\d+)?))?(,|$)/i', (string) $http_accept_encoding, $match)
            && ($match[4] === '' or $match[4] > 0))) {
            $encoding = 'gzip';
        }

        /*
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
