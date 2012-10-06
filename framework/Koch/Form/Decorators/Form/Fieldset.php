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

namespace Koch\Form\Decorators\Form;

use Koch\Form\FormDecorator;

class Fieldset extends FormDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'fieldset';

    public $legend;

    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    public function getLegend()
    {
        return $this->legend;
    }

    public function render($html_form_content)
    {
        $html = '';
        $html .= '<fieldset class="form">';
        $html .= '<legend class="form"><em>' . $this->getLegend() . '</em></legend>';
        $html .= $html_form_content;
        $html .= '</fieldset>';

        return $html;
    }
}
