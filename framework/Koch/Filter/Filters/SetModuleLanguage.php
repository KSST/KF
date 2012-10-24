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
use Koch\Localization\Localization;
use Koch\Router\TargetRoute;

/**
 * Filter for Setting the Module Language.
 *
 * Purpose: Sets the TextDomain for the requested Module
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Filters
 */
class SetModuleLanguage implements FilterInterface
{
    /* @var Koch\Localization */
    private $locale = null;

    public function __construct(Localization $locale)
    {
        // set instance of localization to class
        $this->locale = $locale;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $module = TargetRoute::getController();

        $this->locale->loadTextDomain($module, $this->locale->getLocale(), $module);
    }
}
