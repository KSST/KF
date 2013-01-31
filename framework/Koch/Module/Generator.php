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

namespace Koch\Module;

/**
 * Module Generator
 *
 * Handles
 * - creation of folders,
 * - creation of files of a module.
 *
 * You might select the module with setModule() or via constructor injection.
 */
class Generator
{
    public $structure = array(
        'folders' => array(),
        'files' => array()
    );

    public $module = null;

    public function __construct($module)
    {
        $this->setModule($module);
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function create()
    {
        foreach ($structure['folders'] as $folder) {
            ;
        }

        foreach ($structure['files'] as $file) {
            ;
        }
    }
}
