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

namespace Koch\Filter\Filters;

use Koch\Config\Config;
use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\Validation\InputFilter;

/**
 * Koch Framework - Filter for Theme Selection via URL.
 *
 * Purpose: Sets Theme via URL by appendix $_GET['theme']
 * Usage example: index.php?theme=themename
 * When request parameter 'theme' is set, the user session value for theme will be updated
 */
class ThemeViaGet implements FilterInterface
{
    // default setting
    private $config = [
        'theme_via_get' => 0,
    ];

    private $input = null;

    public function __construct(Config $config, InputFilter $input)
    {
        $config = $config->getApplicationConfig();

        if (isset($config['prefilter'])) {
            $this->config = $config['prefilter'];
        }

        $this->input = $input;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // theme switching must be enabled in configuration
        if ($this->config['theme_via_get'] === 0) {
            return;
        }

        // check for "?theme=mytheme" URL parameter
        if (false === $request->issetParameter('theme', 'GET')) {
            return;
        }

        // get parameter
        $theme = '';
        $theme = $request->getParameterFromGet('theme');

        // Inputfilter for $_GET['theme']. Allowed Chars are: az, 0-9, underscore.
        if (false === $this->input->check($theme, 'is_abc|is_int|is_custom', '_')) {
            throw new \InvalidArgumentException('Please provide a proper theme name.');
        }

        // compose theme dir
        $themedir = '';
        $themedir = APPLICATION_PATH . 'themes/frontend/' . $theme . DIRECTORY_SEPARATOR;

        // if theme exists, set it as frontend theme to the session
        if (is_dir($themedir) and is_file($themedir . 'theme_info.xml')) {
            $_SESSION['user']['frontend_theme'] = $theme;
        }

        unset($theme, $themedir);
    }
}
