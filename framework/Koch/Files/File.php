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
 * Koch Framework - Class for the File Object
 *
 * @category    Koch
 * @package     Core
 * @subpackage  File
 */
class File
{
    protected $name;
    protected $type;
    protected $size;
    protected $temporaryName;
    protected $error;
    protected $allowedExtensions = array();

    /**
     * Constructor.
     *
     * @param $name string
     * @param $type string
     * @param $size int
     * @param $temporaryName string
     * @param $error int
     */
    public function __construct($name, $type, $size, $temporaryName, $error)
    {
        $this->name = basename($name);
        $this->type = $type;
        $this->size = $size;
        $this->temporaryName = $temporaryName;
        $this->error = $error;
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
     * @return integer
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
     * @return integer
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns file extension. If file has no extension, returns null.
     *
     * @return string|null
     */
    public function getExtension()
    {
        return mb_substr($this->name, mb_strrpos($this->name, '.'));
    }

    /**
     * Checks if this file was uploaded successfully.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getError() === UPLOAD_ERR_OK;
    }

    /**
     * Checks if this uploaded file has a valid file extension.
     *
     * @return boolean
     */
    public function hasValidExtension()
    {
        if (count($this->allowedExtensions) === 0) {
            return true;
        } else {
            return in_array($this->getExtension(), $this->allowedExtensions);
        }
    }

    /**
     * Sets the allowed extensions that the uploaded file can have.
     *
     * @param $extensions array  an array of allowed file extensions. If empty,
     * every file extension is allowed.
     * @return Koch_File this object
     */
    public function setAllowedExtensions(array $extensions = array())
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
     * @throws \Koch\Exception\Exception on failure
     */
    public function moveTo($destination, $overwrite = false)
    {
        // ensure upload was valid
        if ( false == $this->isValid()) {
            throw new \Koch\Exception\Exception('File upload was not successful.', $this->getError());
        }

        // ensure a valid file extension was used
        if ( false == $this->hasValidExtension()) {
            throw new \Koch\Exception\Exception('File does not have an allowed extension.');
        }

        // ensure destination directory exists
        if ( false == is_dir($destination)) {
            throw new \Koch\Exception\Exception($destination . ' is not a directory.');
        }

        // ensure destination directory is writeable
        if ( false == is_writable($destination)) {
            throw new \Koch\Exception\Exception('Cannot write to destination directory ' . $destination);
        }

        // check if the destination as a file exists
        if (is_file($destination)) {
            // exit here, if overwrite is not requested
            if (false == $overwrite) {
                throw new \Koch\Exception\Exception('File ' . $destination . ' already exists.');
            }

            if ( false == is_writable($destination)) {
                throw new \Koch\Exception\Exception('Cannot overwrite ' . $destination);
            }
        }

        if ( false == move_uploaded_file($this->temporayName, $destination)) {
            throw new \Koch\Exception\Exception('Moving uploaded file failed.');
        }
    }
}

class Koch_FileFilterIterator extends \FilterIterator
{
    protected $_files;

    public function __construct($iterator, array $files)
    {
        $this->_files = $files;
        parent::__construct($iterator);
    }

    public function accept()
    {
         if (in_array($this->current(), $this->_files) === true) {
             return true;
         } else {
             return false;
         }
    }
}

/**
 * ImagesOnly FileType Filter for the SPL FilterIterator.
 * If the directory iterator is wrapped into this filter, it will fetch only files with a certain type.
 */
class Koch_ImagesOnlyFilterIterator extends \FilterIterator
{
    // whitelist of allowed image filetypes, lowercase
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

        if (in_array($extension, $this->allowed_image_filetypes)) {
            return true;
        } else { // it's not a whitelisted extension

            return false;
        }
    }
}
