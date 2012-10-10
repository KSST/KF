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

namespace Koch\Files;

/**
 * ImagesOnly file type filter for the SPL FilterIterator.
 *
 * If the directory iterator is wrapped into this filter,
 * it will fetch only files with a certain type.
 */
class ImagesOnlyFileFilterIterator extends \FilterIterator
{
    /**
     * @var array Whitelist of allowed image filetypes, lowercase.
     */
    private $allowed_image_filetypes = array('png', 'gif', 'jpeg', 'jpg');

    /**
     * Implements method from FilterIterator (SPL.php)
     */
    public function accept()
    {
        // get the current element from the iterator to examine the fileinfos
        $current = $this->current();

        // we want files, so we skip all directories
        if ($current->getType() !== 'file') {
            return false;
        }

        // set filename and pathinfo
        $filename = $current->getFilename();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // if false, it's not a whitelisted extension
        return (in_array($extension, $this->allowed_image_filetypes)) ? true : false;
    }
}
