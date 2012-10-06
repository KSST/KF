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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\Config;
use Koch\View\Renderer\Smarty;

/**
 * Filter for displaying a maintenace mode screen.
 *
 * Purpose: Display Maintenace Template
 * When config parameter 'maintenance' is set, the maintenance template will be displayed
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Filters
 */
class Maintenance implements FilterInterface
{
    private $config = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // the maintenance mode must be enabled in configuration in order to be displayed
        if ($this->config['maintenance']['enabled'] == 1) {
            return;
        }

        /**
         * @todo b) create override of maintenance mode, in case it's an admin user?
         */

        // fetch renderer
        $smarty = new Smarty($this->config);

        // fetch maintenance template
        $html = $smarty->fetch(ROOT_THEMES_CORE . 'view/smarty/maintenance.tpl', true);

        // output
        $response->setContent($html);
        $response->flush();

        exit();
    }
}
