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

namespace Koch\Config\Adapter;

interface AdapterInterface
{
    public static function readConfig($file);
    public static function writeConfig($file, array $array);
}
