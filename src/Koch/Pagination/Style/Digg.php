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

namespace Koch\Pagination\Style;

use Koch\Pagination\StyleInterface;

/**
 * Class for rending a "Digg" pagination style.
 *
 * @preview [ « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » ]
 */
class Digg implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $current_page  = $pagination->getCurrentPage();
        $next_page     = $pagination->getNextPage();
        $previous_page = $pagination->getPreviousPage();

        $html = '<nav class="pagination">';

        if ($previous_page) {
            $html .= sprintf(
                '<a href="%s">&laquo;&nbsp;%s</a>', str_replace('{page}', $previous_page, $url), _('Previous')
            );
        } else {
            $html .= '&laquo;&nbsp;' . _('Previous');
        }

        if ($total_pages < 13) {

            /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */

            for ($i = 1; $i <= $total_pages; ++$i) {
                if ($i === $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $i, $url), $i);
                }
            }
        } elseif ($current_page < 9) {

            /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */

            for ($i = 1; $i <= 10; ++$i) {
                if ($i === $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $i, $url), $i);
                }
            }

            $html .= '&hellip;';
            $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $total_pages - 1, $url), $total_pages - 1
            );
            $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $total_pages, $url), $total_pages
            );
        } elseif ($current_page > $total_pages - 8) {

            /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */

            $html .= sprintf('<a href="%s">1</a>', str_replace('{page}', 1, $url));
            $html .= sprintf('<a href="%s">2</a>', str_replace('{page}', 2, $url));
            $html .= '&hellip;';

            for ($i = $total_pages - 9; $i <= $total_pages; ++$i) {
                if ($i === $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $i, $url), $i);
                }
            }
        } else {

            /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */

            $html .= sprintf('<a href="%s">1</a>', str_replace('{page}', 1, $url));
            $html .= sprintf('<a href="%s">2</a>', str_replace('{page}', 2, $url));
            $html .= '&hellip;';

            // render page range around the current page
            for ($i = $current_page - 5; $i <= $current_page + 5; ++$i) {
                if ($i === $current_page) {
                    $html .= sprintf('<li class="active">%s</li>', $i);
                } else {
                    $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $i, $url), $i);
                }
            }

            $html .= '&hellip;';
            $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $total_pages - 1, $url), $total_pages - 1);
            $html .= sprintf('<a href="%s">%s</a>', str_replace('{page}', $total_pages, $url), $total_pages);
        }

        if ($next_page) {
            $html .= sprintf('<a href="%s">%s &nbsp;&raquo;</a>', str_replace('{page}', $next_page, $url), _('Next'));
        } else {
            $html .= _('Next') . '&nbsp;&raquo';
        }

        $html .= '</nav';

        return $html;
    }
}
