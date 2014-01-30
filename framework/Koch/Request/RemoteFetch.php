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

namespace Koch\Request;

/**
 * Koch Framework Remote Request Manager
 *
 * 1: Snoppy
 * 2: cURL
 * 3: Remote
 * (4: FTP)
 */
class RemoteFetch
{
    /**
     * Fetches remote content with Snoopy
     *
     * @param $url URL of remote content to fetch
     */
    public static function snoopyGetFile($url)
    {
        $remote_content = null;
       
        $s = new \Snoopy();
        $s->fetch($url);

        if ($s->status == 200) {
            $content = $s->results;
        }
            
        if (false === empty($content)) {
            return $content;
        } 
            
        return false;
    }

    /**
     * Fetches remote content with cURL
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

        if (false === empty($content)) {
            return $content;
        } 
            
        return false;
    }

    /**
     * Fetches remote content with file_get_contents
     *
     * @param $url URL of remote content to fetch
     * @param $flags
     * @param $context
     */
    public static function remoteGetFile($url, $flags = null, $context = null)
    {
        #if(true === ini_get('allow_url_fopen'))
        #{
            $context = stream_context_create(array('http' => array('timeout' => 15)));
            $content = file_get_contents($url, $flags, $context);
        #}

        if (false === empty($content)) {
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
            if (sha1($data) !== sha1_file($local_file)) {
                file_put_contents($local_file, $data);
            }
        }
    }
}
