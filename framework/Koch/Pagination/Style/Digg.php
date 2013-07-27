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
 * Koch Framework - Class for rending a "Digg" pagination style.
 *
 * @preview [ « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » ]
 */
class Digg implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $current_page = $pagination->getCurrentPage();
        $next_page = $pagination->getNextPage();
        $previous_page = $pagination->getPreviousPage();

        $html = '<nav class="pagination">';

        if ($previous_page) {
            $html .= sprintf(
                '<a href="%s">&laquo;&nbsp;%s</a>',
                str_replace('{page}', $previous_page, $url),
                _('Previous')
            );
        } else {
            $html .= '&laquo;&nbsp;' . _('Previous');
        }

        if ($total_pages < 13) {

            /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>',
                        str_replace('{page}', $i, $url),
                        $i
                    );
                }
            }
        } elseif ($current_page < 9) {

            /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */

            for ($i = 1; $i <= 10; $i++) {
                if ($i == $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>',
                        str_replace('{page}', $i, $url), 
                        $i
                    );
                }
            }

            $html .= '&hellip;';
            $html .= sprintf('<a href="%s">%s</a>',
                str_replace('{page}', $total_pages - 1, $url), 
                $total_pages - 1
            );
            $html .= sprintf('<a href="%s">%s</a>', 
                str_replace('{page}', $total_pages, $url), 
                $total_pages
            );
        } elseif ($current_page > $total_pages - 8) {

            /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */

            $html .= sprintf('<a href="%s">1</a>', str_replace('{page}', 1, $url));
            $html .= sprintf('<a href="%s">2</a>', str_replace('{page}', 2, $url));
            $html .= '&hellip;';

            for ($i = $total_pages - 9; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>',
                        str_replace('{page}', $i, $url),
                        $i
                    );
                }
            }
        } else {

            /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */

            $html .= sprintf('<a href="%s">1</a>',
                str_replace('{page}', 1, $url)
            );
            $html .= sprintf('<a href="%s">2</a>',
                str_replace('{page}', 2, $url)
            );
            $html .= '&hellip;';

            // render page range around the current page
            for ($i = $current_page - 5; $i <= $current_page + 5; $i++) {
                if ($i == $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>',
                        str_replace('{page}', $i, $url),
                        $i
                    );
                }
            }

            $html .= '&hellip;';
            $html .= sprintf('<a href="%s">%s</a>',
                str_replace('{page}', $total_pages - 1, $url),
                $total_pages - 1
            );
            $html .= sprintf('<a href="%s">%s</a>',
                str_replace('{page}', $total_pages, $url),
                $total_pages
            );
        }

        if ($next_page) {
            $html .= sprintf('<a href="%s">%s &nbsp;&raquo;</a>',
                str_replace('{page}', $next_page, $url),
                _('Next')
            );
        } else {
            $html .= _('Next') . '&nbsp;&raquo';
        }

        $html .= '</nav';

        return $html;
    }
}
