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
    public static function read($file);

    /**
     * @return boolean|null
     */
    public static function write($file, array $array);
}
