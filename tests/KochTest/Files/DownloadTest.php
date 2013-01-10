<?php

namespace KochTest\Files;

use Koch\Files\Download;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Download
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Download;

        $this->media_files = array(
            // type, mime, binarypacks
            array("jpg", "image/jpeg", array(0xffd8, 0xffe0, 0x0010, 0x4a46, 0x4946, 0x0001, 0x0101, 0x0048, 0x0048)),
            array("png", "image/png", array(0x8950, 0x4e47, 0x0d0a, 0x1a0a, 0x0000, 0x000d, 0x4948, 0x4452, 0x0000)),
            array("mp4", "video/mp4", array(0x0000, 0x001c, 0x6674, 0x7970, 0x6d70, 0x3432, 0x0000, 0x0000, 0x6973)),
            array("mp3", "audio/mpeg", array(0x4944, 0x3303, 0x0000, 0x0000, 0x0f76, 0x5450, 0x4531, 0x0000, 0x000d)),
            array("avi", "video/x-msvideo", array(0x5249, 0x4646, 0x6a42, 0x0100, 0x4156, 0x4920, 0x4c49, 0x5354, 0x8c05)),
            array("ogg", "application/ogg", array(0x4f67, 0x6753, 0x0002, 0x0000, 0x0000, 0x0000, 0x0000, 0x5d28, 0xf95e)),
        );

        vfsStreamWrapper::register();

        // write virtual files
        foreach ($this->media_files as $mime) {
            // extract inner array structure into variables
            list($type, $mimetype, $binary_pack) = $mime;

            $filename = 'file.' . $type;
            $this->file = vfsStream::newFile($filename, 0777)->withContent(
                // write binarypacks to file
                call_user_func_array("pack", array_merge(array("n*"), (array) $binary_pack))
            );
            $this->root = new vfsStreamDirectory('root');
            $this->root->addChild($this->file);
        }

        vfsStreamWrapper::setRoot($this->root);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Files\Download::getMimeType
     */
    public function testGetMimeType()
    {
         foreach ($this->media_files as $mime) {
            // extract inner array structure into variables
            list($type, $mimetype, $binary_pack) = $mime;
            $file = vfsStream::url('root/file.' . $type);
            $fetched_mimetype = $this->object->getMimeType($file);
            $this->assertEquals($mimetype, $fetched_mimetype);
         }
    }

    /**
     * @covers Koch\Files\Download::send
     */
    public function testSend()
    {
         /*list($type, $mimetype, $binary_pack) = $this->media_files[0];
         $filePath = vfsStream::url('root/file.' . $type);
         $rate = 3;
         $this->object->send($filePath, $rate);*/

         // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

    }

    /**
     * @covers Koch\Files\Download::sendFile
     * @todo   Implement testSendFile().
     */
    public function testSendFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
