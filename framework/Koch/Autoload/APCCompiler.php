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

namespace Koch\Autoload;

class APCCompiler
{
    public function compileDir($root, $recursively = true)
    {
        $compiled = true;

        if (true === $recursively) {
            foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $dir) {
                $compiled = $compiled && apc_compile_dir($dir, $recursively);
            }
        } else {
            foreach (glob($root . DIRECTORY_SEPARATOR . '*.php') as $file) {
                $compiled = $compiled && apc_compile_file($file);
            }
        }

        return $compiled;
    }

    public function test()
    {
        echo '<pre>' . PHP_EOL;
        if (function_exists('apc_compile_file')) {

            define('APC_CLEAR_CACHE', true);
            define('APC_COMPILE_RECURSIVELY', true);
            define('APC_COMPILE_DIR', '.');

            echo 'APC Directory Compiler ' . gmdate('Y-m-d H:i:s') . PHP_EOL;
            echo PHP_EOL . '-------------------------' . PHP_EOL;
            if (APC_CLEAR_CACHE) {
                echo (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned') . PHP_EOL;
                var_dump(apc_cache_info());
                echo PHP_EOL . '-------------------------' . PHP_EOL;
            }
            echo 'Runtime Errors' . PHP_EOL;
            if (self::apcCompileDir(APC_COMPILE_DIR, APC_COMPILE_RECURSIVELY) === true) {
                echo 'Cache Created' . PHP_EOL;
            } else {
                echo 'Cache Not Created' . PHP_EOL;
            }
            echo PHP_EOL . '-------------------------' . PHP_EOL;
            var_dump(apc_cache_info());
        } else {
            echo 'APC is not present, nothing to do.' . PHP_EOL;
            echo '</pre>';
        }
    }
}
