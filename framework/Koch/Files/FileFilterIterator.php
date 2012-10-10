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

namespace Koch\Files;

/**
 * File type filter for the SPL FilterIterator.
 *
 * If the directory iterator is wrapped into this filter,
 * it will fetch only files with a certain type.
 */
class FileFilterIterator extends \FilterIterator
{
    protected $files;

    public function __construct($iterator, array $files)
    {
        $this->files = $files;
        parent::__construct($iterator);
    }

    public function accept()
    {
        return (in_array($this->current(), $this->files) === true) ? true : false;
    }
}
