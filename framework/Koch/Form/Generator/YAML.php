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

namespace Koch\Form\Generator;

use Koch\Form\Form;
use Koch\Form\FormGeneratorInterface;

/**
 * Form Generator from and to YAML form description file.
 *
 * 1) Form generation (html representation) from an yaml description file (html = $this->generate(yaml))
 * 2) YAML form description generation from an array description of the form (form(array) ->xml).
 */
class YAML extends Form implements FormGeneratorInterface
{
    // @todo
}
