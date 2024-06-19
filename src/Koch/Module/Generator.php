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

namespace Koch\Module;

/**
 * Module Generator.
 *
 * Handles
 * - creation of folders,
 * - creation of files of a module.
 *
 * You might select the module with setModule() or via constructor injection.
 */
class Generator
{
    public $structure = [
        'folders' => [],
        'files'   => [],
    ];

    public $module;

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
        // @todo

        /*foreach ($structure['folders'] as $folder) {
            ;
        }

        foreach ($structure['files'] as $file) {
            ;
        }*/
    }
}
