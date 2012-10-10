<?php

/**
 * Clansuite - just an eSports CMS
 * Jens-André Koch © 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
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

// Error Reporting Level
error_reporting(E_ALL | E_STRICT);

$root = realpath(dirname((__DIR__)));
$core = "$root/framework";
$test = "$root/tests";

$paths = array(
    $core,
    $test,
    get_include_path()
);

if (!function_exists('_')) {
    function _($msgid)
    {
        return $msgid;
    }
}

// attach original include paths
set_include_path(implode(PATH_SEPARATOR, $paths));

include __DIR__ . '/_autoload.php';

//\Koch\Localization\Utf8::initialize();

