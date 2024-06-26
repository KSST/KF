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
 * Class for the File Object.
 */
class File
{
    protected $name;
    protected $allowedExtensions = [];

    /**
     * Constructor.
     *
     * @param $name string
     * @param $type string
     * @param $size int
     * @param $temporaryName string
     * @param $error int
     */
    public function __construct($name, protected $type, protected $size, protected $temporaryName, protected $error)
    {
        $this->name          = basename((string) $name);
    }

    /**
     * Returns the original filename of the uploaded file.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the MIME type of the uploaded file.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the size of the uploaded file in bytes.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns the temporary filename of the uploade file in server.
     *
     * @return string
     */
    public function getTempName()
    {
        return $this->temporaryName;
    }

    /**
     * Returns the error code associated with this file upload.
     *
     * @see http://www.php.net/manual/en/features.file-upload.errors.php
     *
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns file extension. If file has no extension, returns null.
     *
     * @return string
     */
    public function getExtension()
    {
        return mb_substr((string) $this->name, mb_strrpos((string) $this->name, '.'));
    }

    /**
     * Checks if this file was uploaded successfully.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getError() === UPLOAD_ERR_OK;
    }

    /**
     * Checks if this uploaded file has a valid file extension.
     *
     * @return bool
     */
    public function hasValidExtension()
    {
        if (count($this->allowedExtensions) === 0) {
            return true;
        } else {
            return in_array($this->getExtension(), $this->allowedExtensions, true);
        }
    }

    /**
     * Sets the allowed extensions that the uploaded file can have.
     *
     * @param $extensions array  an array of allowed file extensions. If empty,
     * every file extension is allowed.
     *
     * @return File \Koch\File\File
     */
    public function setAllowedExtensions(array $extensions = [])
    {
        $this->allowedExtensions = $extensions;

        return $this;
    }

    /**
     * Returns the allowed extensions that the uploaded file can have.
     *
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Moves uploaded file to the specified destination directory.
     *
     * @param $destination string  destination directory
     * @param $overwrite boolean overwrite
     *
     * @throws \Koch\Exception\Exception on failure
     */
    public function moveTo($destination, $overwrite = false)
    {
        // ensure upload was valid
        if (false === $this->isValid()) {
            throw new \Koch\Exception\Exception('File upload was not successful.', $this->getError());
        }

        // ensure a valid file extension was used
        if (false === $this->hasValidExtension()) {
            throw new \Koch\Exception\Exception('File does not have an allowed extension.');
        }

        // ensure destination directory exists
        if (false === is_dir($destination)) {
            throw new \Koch\Exception\Exception($destination . ' is not a directory.');
        }

        // ensure destination directory is writeable
        if (false === is_writable($destination)) {
            throw new \Koch\Exception\Exception('Cannot write to destination directory ' . $destination);
        }

        // check if the destination as a file exists
        if (is_file($destination)) {
            // exit here, if overwrite is not requested
            if (false === $overwrite) {
                throw new \Koch\Exception\Exception('File ' . $destination . ' already exists.');
            }

            if (false === is_writable($destination)) {
                throw new \Koch\Exception\Exception('Cannot overwrite ' . $destination);
            }
        }

        if (false === move_uploaded_file($this->temporayName, $destination)) {
            throw new \Koch\Exception\Exception('Moving uploaded file failed.');
        }
    }
}
