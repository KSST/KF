<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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
 */

namespace Koch\Form\Decorators\Form;

use Koch\Form\AbstractFormDecorator;

/**
 * Form_Decorator_Div.
 *
 * Wraps a <div> element around the html_form_content.
 */
class Div extends AbstractFormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'div';

    /**
     * @param string $html_form_content
     */
    public function render($html_form_content)
    {
        // open opening div tag (unclosed first tag)
        $html_deco = CR . '<div';

        // add class
        if (mb_strlen($this->getClass()) > 0) {
            $html_deco .= ' class="' . $this->getClass() . '"';
        }

        // add class
        if (mb_strlen($this->getId()) > 0) {
            $html_deco .= ' id="' . $this->getId() . '"';
        }

        // close opening div tag (close unclosed first tag)
        $html_deco .= '>';

        return  $html_deco . $html_form_content . '</div>' . CR;
    }
}
