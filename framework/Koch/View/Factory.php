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

namespace Koch\View;

/**
 * Class is a Renderer Factory.
 *
 * The static method getRenderer() returns the included and instantiated
 * Rendering Engine Object - which is the View in MVC!
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Renderer
 */
class Factory
{
    /**
     * getRenderer
     *
     * @param $adapter String (A Renderer Name like "smarty", "phptal", "native")
     * @return Renderer Object
     */
    public static function getRenderer($adapter = 'smarty', $injector)
    {
        $file = KOCH_FRAMEWORK . 'View/Renderer/' . ucfirst($adapter) . '.php';

        if (is_file($file) === true) {
            $class = 'Koch\View\Renderer\\' . $adapter;

            if (false === class_exists($class, false)) {
                include $file;
            }

            if (true === class_exists($class, false)) {
                // instantiate and return the renderer and pass Config and Response objects to it
                $view = new $class($injector->instantiate('Koch\Config\Config'));

                return $view;
            } else {
                throw new \Exception('Renderer_Factory -> Class not found: ' . $class, 61);
            }
        } else {
            throw new \Exception('Renderer_Factory -> File not found: ' . $file, 61);
        }
    }
}
