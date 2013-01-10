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

namespace Koch\Files;

/**
 * Class for download handling.
 *
 * Sending of a file to the user may be limited in speed.
 * The class supports the HTTP_RANGE Attribute for parallel and resumed downloads.
 * The class depends on the fileinfo extension (default since php5.3).
 *
 * @link http://www.php.net/manual/en/book.fileinfo.php PHP Manual for the FileInfo Extension
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Download
 */
class Download
{
        /**
     * Returns the mime type of the file.
     *
     * @param  string $file Full path to file.
     * @param  int 0 (full check), 1 (extension check only)
     * @return string MimeType of File.
     */
    public static function getMimeType($file, $mode = 0)
    {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            '3gp' => 'video/3gpp',
            'avi' => 'video/x-msvideo',
            'ogg' => 'application/ogg',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (function_exists('mime_content_type') && $mode == 0) {
            // this is deprecated
            $mimetype = mime_content_type($file);
            return $mimetype;
        } elseif (function_exists('finfo_open') && $mode == 0) {
            // creates a new fileinfo resource
            // and returns the mime type and mime encoding as defined by RFC 2045
            // @see http://php.net/manual/de/fileinfo.constants.php
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mimetype;
        } elseif(array_key_exists($extension, $mime_types)) {
            return $mime_types[$extension];
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * Sends a file as a download to the browser
     *
     * Uses php fileinfo extension to determine the mimetype etc.
     *
     * @param string $filePath The filepath as string
     * @param int    $rate     The speedlimit in KB/s
     */
    private static function sendRated($filePath, $rate = 0)
    {
        // Check if file exists
        if (is_file($filePath) == false) {
            throw new \Koch\Exception\Exception('File not found.');
        }

        // get more information about the file
        $filename = basename($filePath);
        $size = filesize($filePath);
        $mimetype = self::getMimeType($filePath);

        // Create file handle
        $fp = fopen($filePath, 'rb');

        $seekStart = 0;
        $seekEnd = $size;

        /**
         * Check if only a specific part of the file should be sent.
         * The feature names are "multipart-download" and "resumeable-download".
         */
        if ($_SERVER['HTTP_RANGE'] !== null) {
            // calculate the range to use
            $range = explode('-', mb_substr($_SERVER['HTTP_RANGE'], 6));

            $seekStart = intval($range[0]);

            if ($range[1] > 0) {
                $seekEnd = intval($range[1]);
            }

            // Seek to the start
            fseek($fp, $seekStart);

            // Set headers including the range info
            header('HTTP/1.1 206 Partial Content');
            header(sprintf('Content-Range: bytes %d-%d/%d', $seekStart, $seekEnd, $size));
        } else {
            // Set headers for full file
            header('HTTP/1.1 200 OK');
        }

        // Output some headers
        header('Cache-Control: private');
        header('Content-Type: ' . $mimetype);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Description: File Transfer');
        header('Content-Length: ' . ($seekEnd - $seekStart));
        header('Accept-Ranges: bytes');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');

        $block = 1024;

        // limit download speed
        if ($rate > 0) {
            $block *= $rate;
        }

        // disable timeout before download starts
        set_time_limit(0);

        // Send file until end is reached
        while (feof($fp) == false) {
            $timeStart = microtime(true);

            echo fread($fp, $block);
            flush();

            $wait = (microtime(true) - $timeStart) * 1000000;

            // (speed limit) make sure to only send specified bytes per second
            if ($rate > 0) {
                usleep(1000000 - $wait);
            }
        }

        // Close handle
        fclose($fp);
    }

    /**
     * Send a file as a download to the browser
     *
     * @param string $filePath The filepath as string
     * @param int    $rate     The speedlimit in KB/s
     */
    public static function send($filePath, $rate = 3)
    {
        try {
            self::sendRated($filePath, $rate);
        } catch (\Koch\Exception\Exception $e) {
            header('HTTP/1.1 404 File Not Found');
            die('Sorry, an error occured.');

        }
    }

    /**
     * Send a file as a download to the browser
     *
     * @param string $filePath
     * @param int    $rate     speedlimit in KB/s
     */
    public function sendFile($filePath, $rate = 3)
    {
        try {
            self::sendRated($filePath, $rate);
        } catch (\Koch\Exception\Exception $e) {
            header('HTTP/1.1 404 File Not Found');
            die('Sorry, an error occured.');
        }
    }
}
