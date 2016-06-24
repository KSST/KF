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
 * Class for Upload Handling.
 */
class Upload implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $files = [];

    /**
     * Constructor.
     */
    public function __construct($files)
    {
        $this->parseFiles($files);
    }

    /**
     * Parses $files variable to Koch_Upload_File objects.
     */
    protected function parseFiles($files)
    {
        foreach ($files as $formId => $fileInfo) {
            if (is_array($fileInfo['name'])) {
                $this->files[$formId] = [];

                for ($i = 0, $filesTotal = count($files); $i < $filesTotal; ++$i) {
                    $this->files[$formId][$i] = new Koch_File(
                        $fileInfo['name'][$i],
                        $fileInfo['type'][$i],
                        $fileInfo['size'][$i],
                        $fileInfo['tmp_name'][$i],
                        $fileInfo['error'][$i]
                    );
                }
            } else {
                $this->files[$formId] = new Koch_File(
                    $fileInfo['name'],
                    $fileInfo['type'],
                    $fileInfo['size'],
                    $fileInfo['tmp_name'],
                    $fileInfo['error']
                );
            }
        }
    }

    /**
     * Checks whether there is files uploaded with specified name.
     *
     * @param $offset string  form name of file upload
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->files[$offset]);
    }

    /**
     * Returns the uploaded files that have the specified form name.
     *
     * @param $offset string  form name of file upload
     *
     * @return Koch_Upload_File|array an uploaded file object or an array of them
     */
    public function offsetGet($offset)
    {
        return isset($this->files[$offset]) ? $this->files[$offset] : null;
    }

    /**
     * Array access is read only.
     *
     * @throws \Koch\Exception\Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Koch\Exception\Exception('Array access is read only.');
    }

    /**
     * Array access is read only.
     */
    public function offsetUnset($offset)
    {
        throw new \Koch\Exception\Exception('Array access is read only.');
    }

    /**
     * Returns an iterator for uploaded files.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->files);
    }

    /**
     * Returns the count of uploaded files with different form names.
     *
     * @return int
     */
    public function count()
    {
        return count($this->files);
    }
}
