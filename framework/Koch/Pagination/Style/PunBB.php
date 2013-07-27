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

namespace Koch\Pagination\Style;

use Koch\Pagination\StyleInterface;

/**
 * Class for rending a "PunBB" pagination style.
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
            $html .= sprintf('<a href="%s">1</a>', str_replace('{page}', 1, $url));
            if ($current_page != 4) {
                $html .= '&hellip;';
            }
        }

        // render page range around the current page
        for ($i = $current_page - 2, $stop = $current_page + 3; $i < $stop; ++$i) {

            if ($i < 1 or $i > $total_pages) {
                continue;
            }

            if ($current_page == $i) {
                $html .= sprintf('<li class="active">%s</li>', $i);
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
