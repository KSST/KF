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
     * @return    string    the formatted description
     */
    public function getDescription()
    {
        $descriptionField = new FeedHtmlElement($this->description);
        $descriptionField->syndicateHtml = $this->descriptionHtmlSyndicated;
        $descriptionField->truncSize = $this->descriptionTruncSize;
        return $descriptionField->output();
    }
}
