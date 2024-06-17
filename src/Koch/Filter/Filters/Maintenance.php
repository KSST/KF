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

namespace Koch\Filter\Filters;

use Koch\Config;
use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\View\Renderer\Smarty;

/**
 * Filter for displaying a maintenace mode screen.
 *
 * Purpose: Display Maintenace Template
 * When config parameter 'maintenance' is set, the maintenance template will be displayed
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
        if ($this->config['maintenance']['enabled'] === 1) {
            return;
        }

        // fetch renderer
        $smarty = new Smarty($this->config);

        // fetch maintenance template
        $html = $smarty->fetch($this->config['maintenance']['template'], true);

        // output
        $response->setContent($html);
        $response->flush();

        \Koch\Tools\ApplicationQuit::quit();
    }
}
