<?php

namespace KochTest\Request;

// use Koch\Request\FTP;

class FTPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FTP
     */
    protected $object;

    /**
     * Test config data.
     *
     * @var array
     */
    protected $config = [
        'server'   => 'localhost',
        'username' => 'jens',
        'password' => 'pw123', /*,
        'port' => 21,
        'passive' => false*/
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        //$this->object = new FTP($this->config['server'], $this->config['username'], $this->config['password']);
        $this->generator = new \PHPUnit_Framework_MockObject_Generator();
        $mock            = $this->generator->getMock(
            // classname
            'FTPMock',
            // methods
            [
                'upload',
                'download',
                'deleteFile',
                'renameOrMove',
                'createDirectory',
                'isDir',
                'isFile',
                'deleteDirectory',
                'setPermissions',
                'fileSize',
                'getDirectoryContent',
            ],
            // constructor args
            [],
            // classname
            '',
            // invoke (parent)constructor
            false
        );

        $this->object = $mock;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Request\FTP::upload
     */
    public function testUpload()
    {
        // setup method mock
        $this->object->expects($this->any())->method('upload')->will($this->returnValue(true));

        $sourceFile      = 'file.txt';
        $destinationFile = 'file.txt';
        $transferMode    = '1'; // FTP_ASCII = 1, FTP_BINARY = 2

        $result = $this->object->upload($sourceFile, $destinationFile, $transferMode);
        //var_dump($this->object->errors);
        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Request\FTP::download
     */
    public function testDownload()
    {
        // setup method mock
        $this->object->expects($this->any())->method('download')->will($this->returnValue(true));

        $this->object->upload('testfile.txt', 'dir1/testfile.txt');

        $result = $this->object->download('dir1/testfile.txt', 'testfile_downloaded.txt');

        $this->assertTrue($result);
        //$this->assertTrue(is_file('testfile_downloaded.txt'));
    }

    /**
     * @covers Koch\Request\FTP::deleteFile
     */
    public function testDeleteFile()
    {
        // setup method mock
        $this->object->expects($this->any())->method('deleteFile')->will($this->returnValue(true));
        $this->object->expects($this->any())->method('isFile')->will($this->returnValue(false));

        $this->object->upload('testfile.txt', 'dir1/testfile.txt');

        $result = $this->object->deleteFile('dir1/testfile.txt');

        $this->assertTrue($result);
        $this->assertFalse($this->object->isFile('dir1/testfile.txt'));
    }

    /**
     * @covers Koch\Request\FTP::renameOrMove
     */
    public function testRenameOrMove()
    {
        // setup method mock
        $this->object->expects($this->any())->method('renameOrMove')->will($this->returnValue(true));

        $this->object->upload('testfile.txt', 'testfile.txt', 1);

        $result = $this->object->renameOrMove('testfile.txt', 'testfile_renamed.txt');

        $this->assertTrue($result);
    }

    /**
     * @covers Koch\Request\FTP::createDirectory
     */
    public function testCreateDirectory()
    {
        $this->object->expects($this->any())->method('createDirectory')->will($this->returnValue(true));
        $this->object->expects($this->any())->method('isDir')->will($this->returnValue(true));

        $this->object->createDirectory('dir1/dir2/dir3');

        $this->assertTrue($this->object->isDir('dir1/dir2/dir3'));
    }

    /**
     * @covers Koch\Request\FTP::deleteDirectory
     *
     * @todo   Implement testDeleteDirectory().
     */
    public function testDeleteDirectory()
    {
        $this->object->expects($this->any())->method('deleteDirectory')->will($this->returnValue(true));
        $this->object->expects($this->any())->method('isDir')->will($this->returnValue(false));

        $this->object->createDirectory('dir1/dir2/dir3');

        $result = $this->object->deleteDirectory('dir1/dir2');

        $this->assertTrue($result);
        $this->assertFalse($this->object->isDir('dir1/dir2/dir3'));
        $this->assertFalse($this->object->isDir('dir1/dir2'));
    }

    /**
     * @return array
     */
    public function setPermissionsDataprovider()
    {
        return [
            ['111', '111'],
            ['110', '110'],
            ['444', '555'],
            ['412', '512'],
            ['641', '751'],
            ['666', '777'],
            ['400', '500'],
            ['040', '050'],
            ['004', '005'],
        ];
    }
    /**
     * @dataProvider setPermissionsDataprovider
     * @covers Koch\Request\FTP::setPermissions
     */
    public function testSetPermissions($set, $expect)
    {
        // setup method mock
        $this->object->expects($this->any())
                     ->method('setPermissions')
                     ->with('testfile.txt', $this->stringContains($set))
                     ->will($this->returnValue($expect));

        $this->object->upload('testfile.txt', 'testfile.txt');

        $this->assertEquals($this->object->setPermissions('testfile.txt', $set), $expect);
    }

    /**
     * @covers Koch\Request\FTP::isFile
     */
    public function testIsFile()
    {
        $this->object->expects($this->once())->method('upload')->will($this->returnValue(true));
        $this->object->expects($this->any())->method('isFile')->will($this->returnValue(true));

        $result = $this->object->upload('testfile.txt', 'dir1/testfile.txt');

        $this->assertTrue($result);
        $this->assertTrue($this->object->isFile('dir1/testfile.txt'));
    }

    /**
     * @covers Koch\Request\FTP::fileSize
     */
    public function testFileSize()
    {
        $this->object->expects($this->any())->method('fileSize')->will($this->returnValue(123));

        $this->object->upload('testfile.txt', 'testfile.txt', 2);
        $filesize = $this->object->fileSize('testfile.txt');

        $this->assertEquals($filesize, 123); /*filesize('testfile.txt')*/
    }

    /**
     * @covers Koch\Request\FTP::isDir
     *
     * @todo   Implement testIsDir().
     */
    public function testIsDir()
    {
        $this->object->expects($this->any())->method('createDirectory')->will($this->returnValue(true));
        $this->object->expects($this->any())->method('isDir')->will($this->returnValue(true));

        $result = $this->object->createDirectory('dir1');

        $this->assertTrue($result);
        $this->assertTrue($this->object->isDir('dir1'));
    }

    /**
     * @covers Koch\Request\FTP::getDirectoryContent
     */
    public function testGetDirectoryContent()
    {
        $this->object->expects($this->any())->method('getDirectoryContent')->will($this->returnValue(true));

        $result = $this->object->getDirectoryContent('dir1/dir2');
        $this->assertTrue($result);
    }
}
