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
 * Class for rending a "classic" pagination style.
 *
 * @preview [ ‹ First  < 1 2 3 >  Last › ]
 */
class Classic implements StyleInterface
{
    public function render(\Koch\Pagination\Pagination $pagination)
    {
        $lastPage      = $pagination->getLastPage();
        $numberOfPages = $pagination->getNumberOfPages();
        $current_page  = $pagination->getCurrentPage();

        $url = /* Router*/ 'URL';

        $html = '<nav class="pagination">';

        $html .= sprintf('<a href="%s">&lsaquo;&nbsp;%s</a>', str_replace('{page}', 1, $url), _('First'));

        if ($pagination->hasPreviousPage()) {
            $html .= sprintf('<a href="%s">&lt;</a>', str_replace('{page}', $pagination->getPreviousPage(), $url));
        }

        // render page range around the current page
        for ($i = 1; $i <= $numberOfPages; ++$i) {
            if ($i === $current_page) {
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
