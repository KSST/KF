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

namespace Koch\View\Helper;

use Koch\Mvc\Mapper;

/**
 * Class for handling of Widgets.
 */
class Widget
{
    /**
     * loadModul
     *
     * @return boolean
     */
    public static function loadModul($module, $controller)
    {
        $classname = Mapper::mapControllerToClassname($module, $controller);

        // autoload via class_exists
        return class_exists($classname);
    }
}
