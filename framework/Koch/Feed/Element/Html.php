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

namespace Koch\Feed\Element;

/**
 * An Html describes and generates a feed, item or
 * image html field (probably a description).
 * Output is generated based on $truncSize, $syndicateHtml properties.
 */
class Html
{
    /**
     * Mandatory attributes of a Html field.
     */
    public $rawFieldContent;

    /**
     * Optional attributes of a Html field.
     *
     */
    public $truncateSize;
    public $syndicateHtml;

    /**
     * Creates a new instance of FeedHtmlField.
     * @param  $string: if given, sets the rawFieldContent property
     */
    public function __construct($rawFieldContent = null)
    {
        if ($rawFieldContent != null) {
            $this->rawFieldContent = $rawFieldContent;
        }
    }

    /**
     * Creates the right output, depending on $truncSize, $syndicateHtml properties.
     *
     * @return string the formatted field
     */
    public function output()
    {
        // when field available and syndicated in html we assume
        // - valid html in $rawFieldContent and we enclose in CDATA tags
        // - no truncation (truncating risks producing invalid html)
        if (!$this->rawFieldContent) {
            $result = '';
        } elseif ($this->syndicateHtml) {
            $result = '<![CDATA[' . $this->rawFieldContent . ']]>';
        } else {
            if ($this->truncateSize and is_int($this->truncateSize)) {
                $result = \Koch\Feed\AbstractGenerator::iTrunc(
                    htmlspecialchars($this->rawFieldContent), 
                    $this->truncateSize
                );
            } else {
                $result = htmlspecialchars($this->rawFieldContent);
            }
        }

        return $result;
    }
}
