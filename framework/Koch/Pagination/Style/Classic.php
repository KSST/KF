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
 * Koch Framework - Class for rending a "classic" pagination style.
 *
 * @preview [ ‹ First  < 1 2 3 >  Last › ]
 */
class Classic implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $lastPage = $pagination->getLastPage();
        $numberOfPages = $pagination->getNumberOfPages();
        $current_page = $pagination->getCurrentPage();

        $url = /* Router*/ 'URL';

        $html = '<nav class="pagination">';

        $html .= sprintf('<a href="%s">&lsaquo;&nbsp;%s</a>', str_replace('{page}', 1, $url), _('First'));

        if ($pagination->hasPreviousPage()) {
            $html .= sprintf('<a href="%s">&lt;</a>', str_replace('{page}', $pagination->getPreviousPage(), $url));
        }

        // render page range around the current page
        for ($i = 1; $i <= $numberOfPages; $i++) {
            if ($i == $current_page) {
                $html .= sprintf('<li class="active">%s</li>', $i);
            } else {
                $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $i, $url), $i);
            }
        }

        if ($pagination->hasNextPage()) {
            $html .= sprintf('<a href="%s">&gt;</a>', str_replace('{page}', $pagination->getNextPage(), $url));
        }

        if ($lastPage) {
            $html .= sprintf('<a href="%s">&nbsp;%s&rsaquo;</a>', str_replace('{page}', $lastPage, $url), _('Last'));
        }

        $html .= '</nav>';

        return $html;
    }
}
