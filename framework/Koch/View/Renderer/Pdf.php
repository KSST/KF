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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * View Renderer for PDF.
 *
 * This is a wrapper/adapter for the PDF Engine tcpdf.
 *
 * @link http://www.tcpdf.com/ TCPDF Website
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

    public function render($template = null, $viewdata = null)
    {

    }
}
