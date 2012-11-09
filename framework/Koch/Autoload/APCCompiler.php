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
    public static function compileDir($root, $recursively = true)
    {
        if (extension_loaded('apc') === false) {
            throw new \RuntimeException('APC Extensions not loaded.');
        }

        $compiled = true;

        if (true === $recursively) {
            foreach (glob($root . '/*', GLOB_ONLYDIR) as $dir) {
                $compiled = $compiled && apc_compile_dir($dir, $recursively);
            }
        } else {
            foreach (glob($root . '/*.php') as $file) {
                $compiled = $compiled && apc_compile_file($file);
            }
        }

        return $compiled;
    }
}
