<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * This file is part of https://github.com/KSST/KF
 *
 *
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

/**
 * Base Class of all Gettext Extractors
 */
class ExtractorBase
{
    /**
     * @var array Definition of all the tags to scan.
     */
    protected $tags_to_scan;

    /**
     * Add a tag (placeholder/function) to scan for
     *
     * @param mixed|array|string $tag String or Array of Tags.
     *
     * @return Object Koch_Gettext_Extractor
     */
    public function addTags($tags)
    {
        // multiple tags to add
        if (is_array($tags) === true) {
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
     * Excludes a tag from scanning
     *
     * @param string $tag
     *
     * @return Object Koch_Gettext_Extractor
     */
    public function removeTag($tag)
    {
        unset($this->tags_to_scan[$tag]);

        return $this;
    }

    /**
     * Removes all tags
     *
     * @return object Koch_Gettext_Extractor
     */
    public function removeAllTags()
    {
        $this->tags_to_scan = array();

        return $this;
    }
}
