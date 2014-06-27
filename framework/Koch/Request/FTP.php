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

namespace Koch\Request;

/**
 * Koch Framework - Core FTP Class
 *
 * Allows connections to FTP servers and basic directory and file operations.
 */
class FTP
{

    /**
     * @var array $errors An array of any errors
     */
    public $errors = array();

    /**
     * @var ressource
     */
    private $connection;

    /**
     * @var string $server The server hostname to connect to.
     */
    private $server;

    /**
     * @var string $username The username required to access the FTP server.
     */
    private $username;

    /**
     * @var string $password The password required to access the FTP server.
     */
    private $password;

    /**
     * @var int $port The port number to connect to the FTP server on.
     */
    private $port;

    /**
     * @var bool $passive Whether or not to use a passive or active connection.
     */
    private $passive;

    /**
     * Default Constructor
     *
     * @param string $server   The server hostname to connect to.
     * @param string $username The username required to access the FTP server.
     * @param string $password The password required to access the FTP server.
     * @param int    $port     The port number to connect to the FTP server on.
     * @param bool   $passive  Whether or not to use a passive or active connection.
     */
    public function __construct($server, $username, $password, $port = 21, $passive = false)
    {
        if (extension_loaded('ftp') === false) {
            throw new Exception('PHP extension FTP is not loaded.');
        }

        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->passive = $passive;
    }

    /**
     * Tries to
     * (1) open a connection to the remote server
     * (2) authenticate the user
     * (3) set the connection mode
     *
     * @return bool
     */
    private function openConnection()
    {
        // open connection
        if (!$connection = @ftp_connect($this->server, $this->port)) {
            $this->errors[] = 'Cannot connect to FTP Server, please check server and port settings.';
        }

        // authenticate user / login
        if (false === @ftp_login($connection, $this->username, $this->password)) {
            $this->errors[] = 'Connected to server but unable to authenticate the user, please check credentials.';
        }

        // set connection mode
        if (false === @ftp_pasv($connection, $this->passive)) {
            $this->errors[] = 'Unable to set connection mode to passive.';
        }

        if (empty($this->errors) === true) {
            $this->connection = $connection;

            return true;
        }

        return false;
    }

    /**
     * Upload a local file to the remote server
     *
     * @param string $source_file      The local file to upload
     * @param string $destination_file The remote location and name of the file
     * @param int $transfer_mode    optional Defaults to FTP_BINARY(2) connections, but can use FTP_ASCII(1).
     */
    public function upload($source_file, $destination_file, $transfer_mode = 2)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        // check local file
        if (is_file($source_file) === false) {
            $this->errors[] = 'Unable to find local file to send.';

            return false;
        }

        // attempt to send file
        if (false === @ftp_put($this->connection, $destination_file, $source_file, $transfer_mode)) {
            $this->errors[] = 'Unable to send file to remote server, does destination folder exist?';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Download a a file from remote server to local file
     *
     * @param  string $source_file      The remote file
     * @param  string $destination_file The local file to create
     * @param  int $transfer_mode    optional Defaults to FTP_BINARY(2) connections, but can use FTP_ASCII(1).
     * @return bool
     */
    public function download($source_file, $destination_file, $transfer_mode = 2)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        // download file
        if (false === @ftp_get($this->connection, $destination_file, $source_file, $transfer_mode)) {
            $this->errors[] = 'Unable to download file, does local folder exist.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Deletes a remote file
     *
     * @param  string $file The remote file to delete
     * @return bool
     */
    public function deleteFile($file = '')
    {
        if ($this->openConnection() === false) {
            return false;
        }

        // delete file
        if (false === @ftp_delete($this->connection, $file)) {
            $this->errors[] = 'Unable to delete remote file, have you checked permissions.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Rename or move a file or a directory
     *
     * @param  string $source_file  The file or folder to be renamed/moved
     * @param  string $renamed_file The destination or new name of the file/folder
     * @return bool
     */
    public function renameOrMove($source_file, $renamed_file)
    {
        // if source and target files are equal, do nothing and return early
        if ($source_file == $renamed_file) {
            return true;
        }

        if ($this->openConnection() === false) {
            return false;
        }

        if (false === @ftp_rename($this->connection, $source_file, $renamed_file)) {
            $this->errors[] = 'Unable to rename/move file';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Create a remote directory (mkdir)
     *
     * @param  string $dir The path of the remote directory to create
     * @return bool
     */
    public function createDirectory($dir)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        if (ftp_mkdir($this->connection, $dir) === false) {
            $this->errors[] = 'Unable to create remote directory.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Delete a remote directory (rmdir)
     *
     * @param  string $dir The path of the remote directory to delete
     * @return bool
     */
    public function deleteDirectory($dir)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        if (false === @ftp_rmdir($this->connection, $dir)) {
            $this->errors[] = 'Unable to delete remote directory.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Set permissions on a file or directory (chmod)
     *
     * @param  string $file  The file or directory to modify
     * @param  int    $chmod optional The permissions to apply Default 0755
     * @return bool
     */
    public function setPermissions($file, $chmod = 0755)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        if (function_exists('ftp_chmod') === false) {
            if (false === @ftp_site($this->connection, sprintf('CHMOD %o %s', $chmod, $file))) {
                $this->errors[] = 'Unable to modify permissions.';
                $this->closeConnection();

                return false;
            }
        } else {
            if (false === @ftp_chmod($this->connection, $chmod, $file)) {
                $this->errors[] = 'Unable to modify permissions.';
                $this->closeConnection();

                return false;
            }
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Check if a file exists
     *
     * @param  string   $filename The remote file to check
     * @return bool|int FALSE if file doesn't exist or the number of bytes
     */
    public function isFile($filename)
    {
        return $this->fileSize($filename);
    }

    /**
     * Get the size in bytes of a remote file
     * Can be used to check if a file exists
     *
     * @param  string   $filename The remote file to check
     * @return bool|int FALSE if file doesn't exist or the number of bytes
     */
    public function fileSize($filename)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        $fileSize = @ftp_size($this->connection, $filename);

        if ($fileSize === false or $fileSize == -1) {
            $this->errors[] = 'Unable to find remote file.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return $fileSize;
    }

    /**
     * Checks whether a directory exists by trying to navigate to it
     *
     * @param  string $dir The directory to check
     * @return bool
     */
    public function isDir($dir)
    {
        if ($this->openConnection() === false) {
            return false;
        }

        if (false === @ftp_chdir($this->connection, $dir)) {
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return true;
    }

    /**
     * Returns the contents of a directory
     *
     * @param  string     $dir The directory to read
     * @return array|bool An array of files or a FALSE on error
     */
    public function getDirectoryContent($dir)
    {
        $this->openConnection();

        $f = @ftp_nlist($this->connection, $dir);

        if (empty($f) === true) {
            $this->errors[] = 'Unable to read remote directory.';
            $this->closeConnection();

            return false;
        }

        $this->closeConnection();

        return $f;
    }

    /**
     * Attempts to close the connection
     *
     * @return bool
     */
    private function closeConnection()
    {
        if (@ftp_close($this->connection) === false) {
            return false;
        }

        $this->connection = '';

        return true;
    }
}
