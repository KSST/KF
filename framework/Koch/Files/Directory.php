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

class Directory
{
    private $filtername = 'ImagesOnly';
    private $directory = '';

    public function __construct($directory = null)
    {
        if ($directory !== null) {
            $this->setDirectory($directory);
        }

        return $this;
    }

    /**
     * Available Filter types: image
     */
    public function setFilter($filtername)
    {
        $this->filtername = $filtername;

        return $this;
    }

    public function setDirectory($directory)
    {
        // slash fix
        $directory = str_replace('/', DIRECTORY_SEPARATOR, $directory);
        $directory = str_replace('\\', DIRECTORY_SEPARATOR, $directory);

        // prefix directory with ROOT for security purposes
        if (stristr($directory, ROOT) == false) {
            $directory = APPLICATION_PATH . $directory;
        }

        $this->directory = $directory;

        return $this;
    }

    public function getDirectory($directory = '')
    {
        if($directory != '') {
            $this->directory = $directory;
        }

        if (empty($this->directory) === false) {
            return $this->directory;
        } else { // default path

            return APPLICATION_PATH . 'uploads/images/gallery';
        }
    }

    public function getFiles($return_as_array = false)
    {
        // compose the full name of the filter class
        $classname = 'Koch_' . $this->filtername . 'FilterIterator';

        // wrap the iterator in a filter class, when looking for a specific file type, like imagesOnly
        $iterator = new $classname(new \DirectoryIterator($this->getDirectory()));

        // return objects
        if ($return_as_array === false) {
            // create new array to take the SPL FileInfo Objects
            $data = new \ArrayObject();

            // while iterating
            foreach ($iterator as $file) {
                /**
                 * push the SPL FileInfo Objects into the array
                 * @see http://www.php.net/~helly/php/ext/spl/classSplFileInfo.html
                 */
                $data[$file->getFilename()] = $file->getFileInfo();
            }

            $data->ksort();
        } else { // return array
            // create array
            $data = array();

            // while iterating
            foreach ($iterator as $file) {
                $wwwpath = WWW_ROOT . '/' . $this->getDirectory() . '/' . $file->getFilename();
                $wwwpath = str_replace('//', '/', $wwwpath);
                $data[$wwwpath] = $file->getFilename();
            }
        }

        // return the array with SPL FileInfo Objects
        return $data;
    }

    /**
     * Returns the filePath with filename.
     * 
     * @return array pathinfo
     */
    public function filePath($filePath)
    {
        $fileParts = pathinfo($filePath);

        if (!isset($fileParts['filename'])) {
            $fileParts['filename'] = mb_substr($fileParts['basename'], 0, mb_strrpos($fileParts['basename'], '.'));
        }

        return $fileParts;
    }
    
    /**
     * Calculates the size of a directory (recursiv)
     *
     * @param $dir Directory Path
     * @return $size size of directory in bytes
     */
    public static function size($dir)
    {
        if (is_dir($dir) === false) {
            return false;
        }

        $size = 0;        
        $dh = opendir($dir);
        while (($entry = readdir($dh)) !== false) {
            // exclude ./..
            if ($entry == '.' or $entry == '..') {
                continue;
            }

            $direntry = $dir . '/' . $entry;

            if (is_dir($direntry) === false) {
                // recursion
                $size += self::size($direntry);
            } else {
                $size += filesize($direntry);
            }

            unset($direntry);
        }

        closedir($dh);
       
        return $size;
    }
       
    /**
     * Copy a directory recursively
     *
     * @param $source
     * @param $destination
     * @param $overwrite boolean
     */
    public function dirCopy($source, $destination, $overwrite = true)
    {
        $folder_path = '';

        $handle = opendir($source);

        if ($handle === true) {
            while (false !== ( $file = readdir($handle))) {
                if (mb_substr($file, 0, 1) != '.') {
                    $source_path = $source . $file;
                    $target_path = $destination . $file;

                    if (is_file($target_path) === false or $overwrite) {
                        if (array(mb_strstr($target_path, '.') == true)) {
                            $folder_path = dirname($target_path);
                        } else {
                            $folder_path = $target_path;
                        }

                        while(is_dir(dirname(end($folder_path)))
                        and dirname(end($folder_path)) != '/'
                        and dirname(end($folder_path)) != '.'
                        and dirname(end($folder_path)) != ''
                        and ! preg_match('#^[A-Za-z]+\:\\\$#', dirname(end($folder_path))))
                        {
                            array_push($folder_path, dirname(end($folder_path)));
                        }

                        while ($parent_folder_path = array_pop($folder_path)) {
                            if (false === is_dir($parent_folder_path) and
                                false === @mkdir($parent_folder_path, fileperms($parent_folder_path))) {
                                throw new \Exception(
                                    _('Could not create the directory that should be copied (destination).' .
                                      'Probably a permission problem.')
                                );
                            }
                        }

                        $old = ini_set('error_reporting', 0);
                        if (copy($source_path, $target_path) == false) {
                            throw new \Exception(_('Could not copy the directory. Probably a permission problem.'));
                        }
                        ini_set('error_reporting', $old);

                    } elseif (is_dir($source_path) === true) {
                        if (is_dir($target_path) === false) {
                            if (@mkdir($target_path, fileperms($source_path)) == false) {
                              // nope, not an empty if statement :)
                            }
                        }
                        $this->dir_copy($source_path, $target_path, $overwrite);
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * Recursively delete directory using PHP iterators.
     *
     * Uses a CHILD_FIRST RecursiveIteratorIterator to sort files
     * before directories, creating a single non-recursive loop
     * to delete files/directories in the correct order.
     *
     * @param  string $directory
     * @param  bool   $delete_dir_itself
     * @return bool
     */
    public function deleteDir($directory, $delete_dir_itself = false)
    {
        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($ri as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        if ($delete_dir_itself === true) {
            return rmdir($directory);
        }
     }
     
    /**
     * Performs a chmod operation
     *
     * @param $path
     * @param $chmod
     * @param $recursive
     */
    public function chmod($path = '', $chmod = '755', $recursive = false)
    {
        if (is_dir($path) === false) {
            $file_mode = '0' . $chmod;
            $file_mode = octdec($file_mode);

            if (chmod($path, $file_mode) === false) {
                return false;
            }
        } else {
            $dir_mode_r = '0' . $chmod;
            $dir_mode_r = octdec($dir_mode_r);

            if (chmod($path, $dir_mode_r) === false) {
                return false;
            }

            if ($recursive === false) {
                $dh = opendir($path);
                while ($file = readdir($dh)) {
                    if (mb_substr($file, 0, 1) != '.') {
                        $fullpath = $path . '/' . $file;
                        if (!is_dir($fullpath)) {
                            $mode = '0' . $chmod;
                            $mode = octdec($mode);
                            if (chmod($fullpath, $mode) === false) {
                                return false;
                            }
                        } else {
                            if ($this->chmod($fullpath, $chmod, true) === false) {
                                return false;
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }

        return true;
    }
}
