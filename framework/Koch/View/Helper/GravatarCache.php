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

namespace Koch\View\Helper;

/**
 * Gravatar_Cache
 *
 * This is a service class for accessing cached Gravatars as provided
 * by http://www.gravatar.com.
 */
class GravatarCache
{
    // Gravatar Cache Settings
    public $cache_location       = 'Uploads/images';
    public $gravatar_cache_url   = '/gravatars/%s-%s-%s.png';
    public $cache_expire_time    = '7 days';
    public $cacheable            = true;

    // Gravatar Attributes
    public $gravatar_url         = null;
    public $gravatar_id          = null;
    public $size                 = null;
    public $rating               = null;

    public function __construct($gravatarUrl, $gravatarId, $size, $rating)
    {
        $this->gravatar_url = $gravatarUrl;
        $this->gravatar_id  = $gravatarId;
        $this->size         = $size;
        $this->rating       = $rating;
    }

    /**
     * Set absolute Path to the /gravatar_cache folder.
     * Cache might be located on another webserver.
     */
    public function setCacheLocation($path)
    {
        $this->cache_location = $path;
    }

    /**
     * Check if caching is possible.
     */
    public function checkIfCachable()
    {
        if ($this->cacheable == true or 1 == ini_get("allow_url_fopen")) {
            $this->cacheable = true;
        }

        return $this->cacheable;
    }

    /**
     * Gets Gravatar from (1) cache or (2) "gavatar.com"
     */
    public function getGravatar()
    {
        $gravatarFile = sprintf($this->gravatar_cache_url, $this->gravatar_id, $this->size, $this->rating);

        $cacheFileAbsolutePath = APPLICATION_PATH . $this->cache_location . $gravatarFile;
        $cacheFileRelativePath = WWW_ROOT . $this->cache_location . $gravatarFile;

        // if cache file exists and is not expired
        if (is_file($cacheFileAbsolutePath) === true and
            (filemtime($cacheFileAbsolutePath) > strtotime('-' . $this->cache_expire_time)) === true) {
            // return the relative path
            return $cacheFileRelativePath;
        } else {
            // return from gravatar.com
            return $this->setGravatar($cacheFileAbsolutePath, $this->gravatar_url);
        }
    }

    /**
     * sets the specified gravatar at $gravatar_url to the $cache_filename
     */
    public function setGravatar($cacheFile, $gravatarUrl)
    {
        // Check if caching is possible
        if ($this->checkIfCachable() == true) {

            // get the Gravatar and cache it
            $gravatar = file_get_contents($gravatarUrl);
            file_put_contents($cacheFile, $gravatar);
            unset($gravatar);

            chmod($cacheFile, 755);

            // Check if Cache file was created
            if (is_file($cacheFile) === true) {
                return $cacheFile;
            } else {
                // pass-through the original URL
                return $gravatarUrl;
            }
        } else {
             // caching was not possible due to lack of url_fopen
             // passthrough the original URL
             return $gravatarUrl;
        }
    }
}
