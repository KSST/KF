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
