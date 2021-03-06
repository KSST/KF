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

namespace Koch\Form\Decorators\Formelement;

use Koch\Form\AbstractFormElementDecorator;

/**
 * Koch Framework - Formelement Decorator Description.
 *
 * Adds a <span> element containing the formelement
 * description after html_fromelement_content.
 */
class Description extends AbstractFormElementDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'description';

    public $cssClass = 'formdescription';

    /**
     * Renders description *after* formelement.
     */
    public function render($html_formelement)
    {
        if (isset($this->formelement->description)) {
            $html_formelement .= '<br />' . CR;
            $html_formelement .= '<span class="' . $this->cssClass . '">';
            $html_formelement .= $this->formelement->getDescription();
            $html_formelement .= '</span>' . CR;
        }

        return $html_formelement;
    }
}
