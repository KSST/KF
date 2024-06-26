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

namespace Koch\Request;

/**
 * Koch Framework Remote Request Manager.
 *
 * 1: cURL
 * 2: Remote
 */
class RemoteFetch
{
    /**
     * Fetches remote content with cURL.
     *
     * @param $url URL of remote content to fetch
     */
    public static function curlGetFile($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $content = curl_exec($curl);
        curl_close($curl);

        if (false === ($content === false || ($content === '' || $content === '0'))) {
            return $content;
        }

        return false;
    }

    /**
     * Fetches remote content with file_get_contents.
     *
     * @param $url URL of remote content to fetch
     * @param $flags
     * @param $context
     */
    public static function remoteGetFile($url, $flags = null, $context = null)
    {
        #if(true === ini_get('allow_url_fopen'))
        #{
            $context = stream_context_create(['http' => ['timeout' => 15]]);
        $content     = file_get_contents($url, $flags, $context);
        #}

        if (false === ($content === '' || $content === '0' || $content === false)) {
            return $content;
        }

        return false;
    }

    /**
     * Updates a local file with the content of a remote file,
     * when the sha1 checksums do not match.
     */
    public static function updateFileIfDifferent($remote_file, $local_file)
    {
        $data = self::remoteGetFile($remote_file);

        if ($data !== false) {
            if (sha1((string) $data) !== sha1_file($local_file)) {
                file_put_contents($local_file, $data);
            }
        }
    }
}
