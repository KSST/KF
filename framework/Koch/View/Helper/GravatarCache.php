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

namespace Koch\View\Helper;

/**
 * Gravatar_Cache
 *
 * This is a service class for accessing cached Gravatars as provided
 * by http://www.gravatar.com.
 *
 * @package     Koch
 * @subpackage  Libraries
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

    public function __construct( $gravatar_url, $gravatar_id, $size, $rating)
    {
        $this->gravatar_url = $gravatar_url;
        $this->gravatar_id  = $gravatar_id;
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
     * Caching is possible, when we can
     * url_fopen the gravatar.com URL to download from there
     */
    public function checkIfCachable()
    {
        if ($this->cacheable == true or 1 == ini_get("allow_url_fopen") ) {
            $this->cacheable = true;
        }

        return $this->cacheable;
    }

    /**
     * gets a gravatar cache url
     */
    public function getGravatar()
    {
        $gravatar_filename  = '';
        $gravatar_filename .= (string) sprintf($this->gravatar_cache_url,
                                               $this->gravatar_id,
                                               $this->size,
                                               $this->rating);

        // absolute
        $absolute_cache_filename  = '';
        $absolute_cache_filename .= ROOT . $this->cache_location . $gravatar_filename;

        // relative
        $relative_cache_filename  = '';
        $relative_cache_filename .= WWW_ROOT . $this->cache_location . $gravatar_filename;

        // if the cache_file is detected on an absolute path and still in the cache time
        if (is_file($absolute_cache_filename) === true and
           (filemtime($absolute_cache_filename) > strtotime('-' . $this->cache_expire_time)) === true)
        {
            // return it a relative path
            return $relative_cache_filename;
        } else {
            // returnfrom gravatar.com
            return $this->setGravatar($absolute_cache_filename, $this->gravatar_url);
        }
    }

    /**
     * sets the specified gravatar at $gravatar_url to the $cache_filename
     */
    public function setGravatar($cache_filename, $gravatar_url)
    {
        // Check if caching is possible
        if ($this->checkIfCachable() == true) {
            // get the Gravatar and cache it
            file_put_contents($cache_filename, file_get_contents($gravatar_url));

            // Set CHMOD to 755 (rwx r-x r-x)
            chmod($cache_filename, 755);

            // Check if Cache file was created
            if (is_file($cache_filename) === true) {
                return $cache_filename;
            } else {
                // passthrough the original URL
                return $gravatar_url;
            }
        } else {
             // caching was not possible due to lack of url_fopen
             // passthrough the original URL
             return $gravatar_url;
        }
    }
}
