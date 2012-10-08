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

            require 'apc_compile_dir.php';

            echo 'APC Directory Compiler ' . gmdate('Y-m-d H:i:s') . PHP_EOL;
            echo PHP_EOL . '-------------------------' . PHP_EOL;
            if (APC_CLEAR_CACHE) {
                echo (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned') . PHP_EOL;
                var_dump(apc_cache_info());
                echo PHP_EOL . '-------------------------' . PHP_EOL;
            }
            echo 'Runtime Errors' . PHP_EOL;
            echo (apc_compile_dir(APC_COMPILE_DIR, APC_COMPILE_RECURSIVELY) ? 'Cache Created' : 'Cache Not Created') . PHP_EOL;
            echo PHP_EOL . '-------------------------' . PHP_EOL;
            var_dump(apc_cache_info());
        } else {
            echo 'APC is not present, nothing to do.' . PHP_EOL;
            echo '</pre>';
        }
    }

}
