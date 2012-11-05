<?php

namespace KochTest;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    public function testSetup()
    {
        /**
         * a) ensure Koch Framework Autoloader is registered in the spl_autoloader_stack
         */
        $registered_autoloaders = spl_autoload_functions();
        $this->assertEquals('Koch\Autoload\Loader', $registered_autoloaders[0][0]);
        $this->assertEquals('autoload', $registered_autoloaders[0][1]);

        /**
         * b) ensure Koch Framework Constants are set
         */
        $this->assertEquals(true, defined('REWRITE_ENGINE_ON'));
        $this->assertEquals(true, defined('APPLICATION_PATH'));
        $this->assertEquals(true, defined('APPLICATION_CACHE_PATH'));
        $this->assertEquals(true, defined('APPLICATION_MODULES_PATH'));
        #$this->assertEquals(true, defined('VENDOR_PATH'));

        /**
         *  c) ensure /framework and /tests are found on the include path
         */
        $includePath = get_include_path();
        $this->assertContains(realpath(__DIR__ . '/../../framework'), $includePath);
        $this->assertContains(realpath(__DIR__ . '/../../tests'), $includePath);
    }
}
