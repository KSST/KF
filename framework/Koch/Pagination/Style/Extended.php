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
 * Class for rending an "Extended" pagination style.
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
        $html .= sprintf(
            '%s, %s, &ndash; %s %s %s',
            _('Items'),
            $current_first_item,
            $current_last_item,
            _('of'),
            $total_items
        );
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
