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

namespace Koch\Pagination\Style;

use Koch\Pagination\StyleInterface;

/**
 *  * Koch Framework - Class for rending a "PunBB" pagination style.
 *
 * @preview [ Pages: 1 … 4 5 6 7 8 … 15 ]
 */
class PunBB implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $current_page = $pagination->getCurrentPage();
        $total_pages;

        $html = '<nav class="pagination">' . _('Pages');

	    if ($current_page > 3) {
            $html .= sprintf('<a href="%s>1</a>', str_replace('{page}', 1, $url));
            if ($current_page != 4) {
                $html .= '&hellip;';
            }
	    }

        for ($i = $current_page - 2, $stop = $current_page + 3; $i < $stop; ++$i) {

            if ($i < 1 OR $i > $total_pages) {
                continue;
            }

            if ($current_page == $i) {
                $html .= '<strong>'.$i.'</strong>';
            } else {
                $html .= sprintf('<a href="%s">$s</a>', str_replace('{page}', $i, $url), $i);
            }

        }


        if ($current_page <= $total_pages - 3) {
            if ($current_page != $total_pages - 3) {
                $html .= '&hellip;';
            }
            $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $total_pages, $url), $total_pages);
        }

        $html .= '</nav>';

        return $html;
    }
}