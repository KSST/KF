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

namespace Koch\Files;

/**
 * Koch Framework - Class for download handling.
 *
 * Sending of a file to the user may be limited in speed.
 * The class supports the HTTP_RANGE Attribute for parallel and resumed downloads.
 * The class depends on the fileinfo extension (default since php5.3).
 *
 * @link http://www.php.net/manual/en/book.fileinfo.php PHP Manual for the FileInfo Extension
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

        if (function_exists('finfo_open') === true && $mode == 0) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $file);
            finfo_close($finfo);

            return $mimetype;
        } elseif (array_key_exists($extension, $mime_types)) {
            return $mime_types[$extension];
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * Sends a file as a download to the browser
     *
     * @param string $file Filepath.
     * @param int    $rate The speedlimit in KB/s
     */
    public static function sendFile($file, $rate = 0)
    {
        if (is_file($file) === false) {
            throw new \InvalidArgumentException('File "' . $file . '" not found.');
        }

        try {
            // get more information about the file
            $filename = basename($file);
            $size = filesize($file);
            $mimetype = self::getMimeType($file);

            // create file handle
            $fp = fopen($file, 'rb');

            $seekStart = 0;
            $seekEnd = $size;

            /**
             * Check if only a specific part of the file should be sent.
             * The feature names are "multipart-download" and "resumeable-download".
             */
            if (isset($_SERVER['HTTP_RANGE']) === true) {
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
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');

            $block = 1024;

            // limit download speed
            if ($rate > 0) {
                $block *= $rate;
            }

            // disable timeout before download starts
            set_time_limit(0);

            // Send file until end is reached
            while (feof($fp) === false) {
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
        } catch (\Koch\Exception\Exception $e) {
            // Error, while downloading the file.;
            header('HTTP/1.1 404 File Not Found');            
        }
    }
}
