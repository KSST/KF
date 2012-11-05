<?php

namespace KochTest\Autoload;

use Koch\Autoload\Loader;

/**
 * Interface and Class definition for testing
 * the autoload skipping, when "already loaded".
 */
interface ThisInterfaceExists
{

}

class ThisClassExists
{

}

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public $classMapFile = 'autoloader.classmap.php';

    public function setUp()
    {
        parent::setUp();

        // Fixtures
        set_include_path(realpath(__DIR__ . '/fixtures') . PATH_SEPARATOR . get_include_path());

        /**
         * The APC user cache needs a reset, so that the map is generated freshly each run.
         * APC is used by readAutoloadingMapApc() / writeAutoloadingMapApc().
         */
        self::apcClearCache();

        Loader::setClassMapFile($this->classMapFile);
    }

    public function tearDown()
    {
        self::apcClearCache();
    }

    public static function apcClearCache()
    {
        if (extension_loaded('apc') and ini_get('apc.enabled') and ini_get('apc.enable_cli')) {
            apc_clear_cache('user');
        }
    }

    /**
     * testMethod_autoload()
     */
    public function testMethod_autoload()
    {
        // workflow of autoloading

        // 1. existing class
        // 2. existing interface
        // 3. existing trait

        // 1. autoloadExclusions()
        // 2. autoloadInclusions()
        // 3. autoloadByApcOrFileMap()
        // 4. autoloadIncludePath()
        // 5. autoloadTryPathsAndMap()
    }

    public function testMethod__construct()
    {
        // unregister first (autoloader was registered during test setup)
        $r = spl_autoload_unregister(array('Koch\Autoload\Loader', 'autoload'));

        // registers autoloader via constructor
        new Loader;

         // test Koch Framework Autoloader is registered in the spl_autoloader_stack at first place
        $registered_autoloaders = spl_autoload_functions();
        $this->assertEquals('Koch\Autoload\Loader', $registered_autoloaders[0][0]);
        $this->assertEquals('autoload', $registered_autoloaders[0][1]);
    }

    /**
     * testMethod_autoloadExclusions()
     */
    public function testMethod_autoloadExclusions()
    {
        // exclude "Smarty_Internal" classes
        $this->assertTrue(Loader::autoloadExclusions('Smarty_Internal_SomeClass'));

        // exclude "Doctrine" classes
        $this->assertTrue(Loader::autoloadExclusions('Doctrine_SomeClass'));

        // but not, our own namespaced doctrine classes "Koch\Doctrine\"
        $this->assertFalse(Loader::autoloadExclusions('Koch\Doctrine\SomeClass'));

        // exclude "Smarty" classes
        $this->assertTrue(Loader::autoloadExclusions('Smarty_'));

        // but not, our own smarty class "\Smarty"
        $this->assertFalse(Loader::autoloadExclusions('Koch\View\Renderer\Smarty'));
    }

    /**
     * testMethod_autoloadInclusions()
     */
    public function testMethod_autoloadInclusions()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadInclusions('SomeUnknownClass'));

        // try to load "Clansuite_Staging" class
        // no definitions atm
        #$this->assertTrue(Loader::autoloadInclusions('Clansuite_Staging'));
    }

    /**
     * testMethod_autoloadByApcOrFileMap
     */
    public function testMethod_autoloadByApcOrFileMap()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadByApcOrFileMap('SomeUnknownClass'));

        Loader::addMapping('Sysinfo', realpath(__DIR__ . '/../../../framework/Koch/Tools/SysInfo.php'));
        $this->assertTrue(Loader::autoloadByApcOrFileMap('Sysinfo'));
    }

    /**
     * testMethod_autoloadIncludePath()
     */
    public function testMethod_autoloadIncludePath()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadIncludePath('\Namespace\Library\SomeUnknownClass'));

        // set the include path to our fixtures directory, where a namespaces class exists
        $path = __DIR__ . '/fixtures/Clansuite';
        set_include_path($path . PATH_SEPARATOR . get_include_path());

        // try to load existing namespaced class
        $this->assertTrue(Loader::autoloadIncludePath('NamespacedClass'));
   }

   public function testMethod_writeAutoloadingMapFile()
   {
        if (is_file($this->classMapFile)) {
            unlink($this->classMapFile);
        }

        // file will be created
        $this->assertSame(array(), Loader::readAutoloadingMapFile());
        $this->assertTrue(is_file($this->classMapFile));

        $array = array('class' => 'file');
        $this->assertTrue(Loader::writeAutoloadingMapFile($array));
        $this->assertSame($array, Loader::readAutoloadingMapFile());
    }

    public function testMethod_readAutoloadingMapFile()
    {
        if (is_file($this->classMapFile)) {
            unlink($this->classMapFile);
        }
        // file will be created
        $this->assertSame(array(), Loader::readAutoloadingMapFile());
        $this->assertTrue(is_file($this->classMapFile));

        $array = array ( 'class' => 'file' );
        $this->assertTrue(Loader::writeAutoloadingMapFile($array));
        $this->assertSame($array, Loader::readAutoloadingMapFile());
    }

    public function testMethod_writeAutoloadingMapApc()
    {
        if (extension_loaded('apc')) {
            $array = array ( 'class' => 'file' );
            $this->assertTrue(Loader::writeAutoloadingMapApc($array));
            $this->assertSame($array, Loader::readAutoloadingMapApc());
        }
    }

    public function testMethod_readAutoloadingMapApc()
    {
        if (extension_loaded('apc')) {
            $this->assertSame(apc_fetch('CLANSUITE_CLASSMAP'), Loader::readAutoloadingMapApc());
        }
    }

    public function testMethod_addMapping()
    {
        $class = 'addToMappingClass';
        $file = realpath(__DIR__ . '/fixtures/notloaded/addToMapping.php');

        $this->assertTrue(Loader::addMapping($class, $file));

        // test if the entry was added to the autoloader class map array
        $map = Loader::getAutoloaderClassMap();
        // entry exists
        $this->assertTrue(true, array_key_exists($class, $map));
        // compare entries
        $this->assertEquals($map[$class], $file);

        // file not loaded, just mapped
        #$this->assertFalse(class_exists($class, false));

        // triggering autoload via class_exists
        // --- WARNING ---
        // The "Koch Framework Autoloader" needs to be registered BEFORE "Composers Autoloader".
        $this->assertTrue(class_exists($class, true));
    }

    public function testMethod_includeFileAndMap()
    {
        $file = realpath(__DIR__ . '/fixtures/includeFileAndMap.php');
        $class = 'includeFileAndMapClass';

        Loader::includeFileAndMap($file, $class);

        // test if the entry was added to the autoloader class map array
        $map = Loader::getAutoloaderClassMap();

        $this->assertTrue(true, array_key_exists($class, $map));

        $this->assertEquals($map[$class],$file);

        // file already loaded
        $this->assertTrue(class_exists($class, false));
    }

    public function testMethod_includeFile()
    {
        // a) include file
        $this->assertTrue(Loader::includeFile(__DIR__ . '/fixtures/ClassForRequireFile1.php'));

        // b) include class
        $this->assertTrue(Loader::includeFile(__DIR__ . '/fixtures/ClassForRequireFile2.php', 'ClassForRequireFile2'));

        // c) include class (second parameter), but class does not exist
        $this->assertFalse(Loader::includeFile('nonExistantFile.php'), 'ThisClassDoesNotExist');

        // d) file not found returns false
        $this->assertFalse(Loader::includeFile('nonExistantFile.php'));
    }
}
