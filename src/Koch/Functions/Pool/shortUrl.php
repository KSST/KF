<?php

/**
 * Koch Framework
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 *
 * This file is part of https://github.com/KSST/KF
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * */

/**
 * Shortens a URL via TinyURL Service.
 *
 * @param <type> $long_url The long URL you want to shorten.
 *
 * @return string A Shortened URL via TinyURL Service
 */
function shortUrl($long_url)
{
    $long_url = urlencode((string) $long_url);

    $handle = '';
    $handle = fopen('http://tinyurl.com/api-create.php?url=' . $long_url, 'rb');

    if ($handle) {
        $short_url = '';
        while (false === feof($handle)) {
            $short_url .= fgets($handle, 2000);
        }
        fclose($handle);
    } else {
        throw new \Koch\Exception\Exception('Unable to shorten the link.');
    }

    return $short_url;
}
