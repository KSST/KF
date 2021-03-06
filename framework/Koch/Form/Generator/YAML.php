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

namespace Koch\Form\Generator;

use Koch\Form\Form;
use Koch\Form\FormGeneratorInterface;

/**
 * Koch Framework - Form Generator from and to YAML form description file.
 *
 * 1) Form generation (html representation) from an yaml description file (html = $this->generate(yaml))
 * 2) YAML form description generation from an array description of the form (form(array) ->xml).
 */
class YAML extends Form implements FormGeneratorInterface
{
    // @todo
}
