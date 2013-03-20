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
 */

namespace Koch\Feed;

/**
 * An HtmlDescribable is an item within a feed
 * that can have a description that may include HTML markup.
 */
class ElementBase
{
    /**
     * Indicates whether the description field should be rendered in HTML.
     */
    public $descriptionHtmlSyndicated;

    /**
     * Indicates whether and to how many characters a description should be truncated.
     */
    public $descriptionTruncSize;

    /**
     * Returns a formatted description field, depending on descriptionHtmlSyndicated and
     * $descriptionTruncSize properties
     *
     * @return string the formatted description
     */
    public function getDescription()
    {
        $descriptionField = new Koch\Feed\Element\Html($this->description);
        $descriptionField->syndicateHtml = $this->descriptionHtmlSyndicated;
        $descriptionField->truncateSize = $this->descriptionTruncSize;

        return $descriptionField->output();
    }
}
