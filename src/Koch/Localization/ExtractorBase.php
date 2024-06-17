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

namespace Koch\Localization;

/**
 * Base Class of all Gettext Extractors.
 */
class ExtractorBase
{
    /**
     * @var array Definition of all the tags to scan.
     */
    protected $tags_to_scan;

    /**
     * Add a tag (placeholder/function) to scan for.
     *
     * @param mixed|array|string $tags String or Array of Tags.
     *
     * @return ExtractorBase Koch_Gettext_Extractor
     */
    public function addTags($tags)
    {
        // multiple tags to add
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                if (false === array_key_exists($tag, array_flip($this->tags_to_scan))) {
                    $this->tags_to_scan[] = $tag;
                }
            }
        } else { // just one element (string)
            $this->tags_to_scan[] = $tags;
        }

        return $this;
    }

    /**
     * Excludes a tag from scanning.
     *
     * @param string $tag
     *
     * @return ExtractorBase Koch_Gettext_Extractor
     */
    public function removeTag($tag)
    {
        unset($this->tags_to_scan[$tag]);

        return $this;
    }

    /**
     * Removes all tags.
     *
     * @return ExtractorBase Koch_Gettext_Extractor
     */
    public function removeAllTags()
    {
        $this->tags_to_scan = [];

        return $this;
    }
}
