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
    private $allowed_image_filetypes = ['png', 'gif', 'jpeg', 'jpg'];

    /**
     * Implements method from FilterIterator (SPL.php).
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
        $filename  = $current->getFilename();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // if false, it's not a whitelisted extension
        return (in_array($extension, $this->allowed_image_filetypes, true)) ? true : false;
    }
}
