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
use Koch\Config\Config;
use Koch\Validation\InputFilter;

/**
 * Filter for Theme Selection via URL.
 *
 * Purpose: Sets Theme via URL by appendix $_GET['theme']
 * Usage example: index.php?theme=themename
 * When request parameter 'theme' is set, the user session value for theme will be updated
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Filters
 */
class ThemeViaGet implements FilterInterface
{
    private $config     = null;
    private $input      = null;

    public function __construct(Config $config, Inputfilter $input)
    {
        // reduce array size by selection of the section
        $this->config = $config['prefilter'];
        $this->input  = $input;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // themeswitching must is enabled in configuration
        if ($this->config['theme_via_get'] == 1) {
            return;
        }

        // check for "?theme=mytheme" URL parameter
        if (false === $request->issetParameter('theme', 'GET')) {
            return;
        }

        $theme = '';
        $theme = $request->getParameterFromGet('theme');

        /**
         * Inputfilter for $_GET['theme']. Allowed Chars are: az, 0-9, underscore.
         */
        if (false === $this->input->check($theme, 'is_abc|is_int|is_custom', '_' )) {
            throw new \InvalidArgumentException('Please provide a proper theme name.');
        }

        $themedir = '';
        $themedir = ROOT_THEMES_FRONTEND . $theme . DIRECTORY_SEPARATOR;

        // theme exists, set it as session-user-theme
        if (is_dir($themedir) and is_file($themedir . 'theme_info.xml')) {
            $_SESSION['user']['frontend_theme'] = $theme;
        }

        unset($theme, $themedir);
    }
}
