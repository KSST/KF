<?php

namespace KochTest\Config;

use Koch\Config\Staging;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class StagingTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @var Staging
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Staging();

        // set faked server name to environment to test getFilename()
        $_SERVER['SERVER_NAME'] = 'www.application-dev.com';

        vfsStreamWrapper::register();

        $this->fileURL = vfsStream::url('root/development.ini.php');
        $this->file    = vfsStream::newFile('development.ini.php', 0777)->withContent($this->getDevConfigFileContent());
        $this->root    = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * @covers Koch\Config\Staging::overloadWithStagingConfig
     */
    public function testOverloadWithStagingConfig()
    {
        $array_to_overload = [
            // new key
            'overloaded-key' => 'overloaded-value',
            // overload existing key value
            'error' => ['development' => '0'],
        ];

        // manually set the config for overloading
        Staging::setFilename($this->fileURL);
        $overloaded_cfg = Staging::overloadWithStagingConfig($array_to_overload);

        // new key exists
        $this->assertTrue(array_key_exists('overloaded-key', $overloaded_cfg));
        // new key has correct value
        $this->assertEquals($overloaded_cfg['overloaded-key'], $array_to_overload['overloaded-key']);

        // overloading of key ['error']['development']
        // original value is 0
        $this->assertEquals($array_to_overload['error']['development'], '0');
        // expect that error array is present
        $this->assertTrue(array_key_exists('error', $overloaded_cfg));
        // expect that error array has a key developement
        $this->assertTrue(array_key_exists('development', $overloaded_cfg['error']));
        // expect that this key is set to 1 (on)
        $this->assertEquals($overloaded_cfg['error']['development'], '1');
        // expect that both values are not equal
        $this->assertNotEquals($overloaded_cfg['error']['development'], $array_to_overload['error']['development']);
    }

    /**
     * @covers Koch\Config\Staging::getFilename
     */
    public function testGetFilename()
    {
        // test that the related development config exists
        $expected_filename = $this->fileURL;
        $this->assertFileExists($expected_filename);
        $this->assertEquals(Staging::getFilename(), 'vfs://root/development.ini.php');

        // automatically determine the config for overloading from SERVER_NAME
        Staging::setFilename(null);
        $_SERVER['SERVER_NAME'] = 'blabla';
        $this->assertEquals(Staging::getFilename(), 'production.php');  // default is production

        Staging::setFilename(null);
        $_SERVER['SERVER_NAME'] = 'localhost';
        $this->assertEquals(Staging::getFilename(), 'development.php');

        Staging::setFilename(null);
        $_SERVER['SERVER_NAME'] = 'application-stage.com';
        $this->assertEquals(Staging::getFilename(), 'staging.php');

        Staging::setFilename(null);
        $_SERVER['SERVER_NAME'] = 'application-intern.com';
        $this->assertEquals(Staging::getFilename(), 'intern.php');
    }

    /**
     * @covers Koch\Config\Staging::setFilename
     */
    public function testSetFilename()
    {
        $expected_filename = __DIR__ . '/fixtures/development.php';

        Staging::setFilename($expected_filename);
        $filename = Staging::getFilename();

        $this->assertEquals($filename, $expected_filename);
        $this->assertEquals($filename, $expected_filename);
    }

    public function getDevConfigFileContent()
    {
        return <<<EOF
; <?php die( 'Access forbidden.' ); /* DO NOT MODIFY THIS LINE! ?>
;
; Koch Framework Configuration File : Development
; This file was generated on 26-08-2012 13:58
;

;----------------------------------------
; database
;----------------------------------------
[database]
host = "localhost"
driver = "pdo_mysql"
user = "root"
password = 123
dbname = "applicationtest"
prefix = "cs_"
charset = "UTF8"

;----------------------------------------
; error
;----------------------------------------
[error]
debug = 1
xdebug = 0
development = 1
debug_popup = 0
webdebug = 0
help_edit_mode = 0
compression = 0

; DO NOT REMOVE THIS LINE */ ?>
EOF;
    }
}
