<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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
 *
 */

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for PDF.
 *
 * This is a wrapper/adapter for the PDF Engine tcpdf.
 *
 * @link http://www.tcpdf.com/ TCPDF Website
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Pdf extends AbstractRenderer
{
    /* @var \mPDF */
    public $renderer = null;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        // composer autoload not working, due to missing autoload section in mpdfs composer.json
        /*include_once VENDOR_PATH . '/mpdf/mpdf/mpdf.php';
        $mpdf = new \mPDF();
        $mpdf->WriteHTML('<p>Your first taste of creating PDF from HTML</p>');
        $mpdf->Output();*/
    }

    public function initializeEngine($template = null)
    {

    }

    public function configureEngine()
    {

    }

    public function assign($tpl_parameter, $value = null)
    {

    }

    public function display($template, $viewdata = null)
    {

    }

    public function fetch($template, $viewdata = null)
    {

    }

    public function render($template, $viewdata = null)
    {

    }
}
