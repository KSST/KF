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

namespace Koch\View;

/**
 * Class is a Renderer Factory.
 *
 * The static method getRenderer() returns the included and instantiated
 * Rendering Engine Object - which is the View in MVC!
 */
class Factory
{
    /**
     * getRenderer.
     *
     * @param $adapter String (A Renderer Name like "smarty", "phptal", "native")
     * @param $injector DI
     *
     * @return Renderer Object
     */
    public static function getRenderer($adapter = 'smarty', $injector = null)
    {
        $adapter = ucfirst((string) $adapter);

        $file = realpath(__DIR__ . '/Renderer/' . $adapter . '.php');

        if (is_file($file)) {
            $class = 'Koch\View\Renderer\\' . $adapter;

            if (false === class_exists($class, false)) {
                include $file;
            }

            if (true === class_exists($class, false)) {
                // instantiate and return the renderer and pass Config and Response objects to it
                $view = new $class($injector->instantiate(Koch\Config\Config::class));

                return $view;
            } else {
                throw new \Exception('Renderer_Factory -> Class not found: ' . $class, 61);
            }
        } else {
            throw new \Exception('Renderer_Factory -> File not found: ' . $file, 61);
        }
    }
}
