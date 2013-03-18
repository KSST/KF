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

namespace Koch\Logger;

/**
 * A Factory for Logger Adapters.
 */
class Factory
{
    /**
     * Returns the requested Logger adapter.
     * 
     * @param type $adapter (File, Email, Firebug)
     * @return \Koch\Logger\class
     */
    public function getAdapter($adapter)
    {
        $classname = ucfirst($adapter);
        $file = __DIR__ . '/Adapter/'. $classname;
        $class = '\Koch\Logger\Adapter\\' . $classname;
        if (false === class_exists($class, false)) {
            include $file;
        }

        return new $class;
    }
}
