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
    public $truncSize;
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
            if ($this->truncSize and is_int($this->truncSize)) {
                $result = FeedCreator::iTrunc(htmlspecialchars($this->rawFieldContent), $this->truncSize);
            } else {
                $result = htmlspecialchars($this->rawFieldContent);
            }
        }

        return $result;
    }
}
