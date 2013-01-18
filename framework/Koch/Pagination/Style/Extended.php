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
 *  * Koch Framework - Class for rending an "Extended" pagination style.
 *
 * @preview  [ « Previous | Page 2 of 11 | Showing items 6-10 of 52 | Next » ]
 */
class Extended implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $html = '<nav class="pagination">';

        if ($previous_page) {
            $html .= sprintf(
                '<a href="%s">&laquo; %s &nbsp;</a>',
                str_replace('{page}', $pagination->getPreviousPage(), $url),
                _('previous')
            );
        } else {
            $html .= '&laquo; &nbsp; ' . _('previous');
        }

        $html .= '|';
        $html .= sprintf('%s, %s, %s, %s', _('Page'), $current_page, _('of'), $total_pages);
        $html .= '|';
        $html .= sprintf('%s, %s, &ndash; %s %s %s', _('Items'), $current_first_item, $current_last_item, _('of'), $total_items);
        $html .= '|';

        if ($pagination->hasNextPage()) {
            $html .= sprintf(
                '<a href="%s">&nbsp; %s &raquo;</a>',
                str_replace('{page}', $pagination->getNextPage(), $url),
                _('Next')
            );
        } else {
            $html .= _('pagination.next') . '&nbsp;&raquo;';
        }

        $html .= '</nav>';

        return $html;
    }
}
