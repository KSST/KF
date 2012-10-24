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
 * View Renderer for Xtemplate templates.
 *
 * This is a wrapper/adapter for rendering with XTemplate.
 *
 * @link http://www.phpxtemplate.org/ Offical Website of PHP XTemplate
 * @link http://xtpl.sourceforge.net/ Project's Website at Sourceforge
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Xtemplate extends AbstractRenderer
{
    public function __construct(Koch\Config $config)
    {
        parent::__construct($config);
    }

    public function initializeEngine($template = null)
    {
        $xtpl = VENDOR_PATH . '/xtemplate/xtemplate.class.php';

        // prevent redeclaration
        if (class_exists('XTemplate', false) == false) {
            // check if library exists
            if (is_file($xtpl) === true) {
                include $xtpl;
            } else {
                throw new Exception('XTemplate Library missing!');
            }
        }

        $template = $this->getTemplatePath($template);

        #\Koch\Debug\Debug::firebug('Xtemplate loaded with Template: ' . $template);

        // Do it with XTemplate style > eat like a bird, poop like an elefant!
        return $this->renderer = new XTemplate($template);
    }

    public function configureEngine()
    {

    }

    public function renderPartial($template)
    {

    }

    public function clearVars()
    {

    }

    public function clearCache()
    {

    }

    public function fetch($template, $data = null)
    {

    }

    public function display($template, $data = null)
    {

    }

    /**
     * Returns a clean xTemplate Object
     *
     * @return Smarty Object
     */
    public function getEngine()
    {
        // clear assigns?
        return $this->renderer;
    }

    public function render($template, $viewdata)
    {
        $this->renderer->assign($viewdata);
        $this->renderer->parse($template);
        $this->renderer->out($template);
    }

    public function assign($tpl_parameter, $value = null)
    {
        $this->renderer->assign($tpl_parameter, $value);
    }
}
