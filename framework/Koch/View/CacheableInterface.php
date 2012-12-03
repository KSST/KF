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

namespace Koch\View;

interface CacheableInterface
{
    /**
     * Enables / disables the caching of templates.
     */
    public function activateCaching($bool);

    /**
     * Checks if a template is cached.
     *
     * @param  string  $template   the resource handle of the template file or template object
     * @param  mixed   $cache_id   cache id to be used with this template
     * @param  mixed   $compile_id compile id to be used with this template
     * @return boolean Returns true in case the template is cached, false otherwise.
     */
    public function isCached($template, $cache_id = null, $compile_id = null);

    /**
     * Reset the Cache of the Renderer
     */
    public function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null);
}
