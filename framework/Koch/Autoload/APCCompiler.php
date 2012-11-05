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
        echo '<pre>' . "\n";
        if (function_exists('apc_compile_file')) {

            define('APC_CLEAR_CACHE', true);
            define('APC_COMPILE_RECURSIVELY', true);
            define('APC_COMPILE_DIR', '.');

            echo 'APC Directory Compiler ' . gmdate('Y-m-d H:i:s') . "\n";
            echo "\n" . '-------------------------' . "\n";
            if (APC_CLEAR_CACHE) {
                echo (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned') . "\n";
                var_dump(apc_cache_info());
                echo "\n" . '-------------------------' . "\n";
            }
            echo 'Runtime Errors' . "\n";
            if (self::apcCompileDir(APC_COMPILE_DIR, APC_COMPILE_RECURSIVELY) === true) {
                echo 'Cache Created' . "\n";
            } else {
                echo 'Cache Not Created' . "\n";
            }
            echo "\n" . '-------------------------' . "\n";
            var_dump(apc_cache_info());
        } else {
            echo 'APC is not present, nothing to do.' . "\n";
            echo '</pre>';
        }
    }
}
