<?php
//require 'PHPUnit/Autoload.php';

require __DIR__ . '/bootstrap.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'KochTest\AllTests::main');
}

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Koch Framework - TestSuite');
        foreach (self::getTestFiles() as $file) {
            $suite->addTestFile($file);
        }

        return $suite;
    }

    /**
     * Tries to find in CLI parameters and returns array of files to be runj by PHPUnit
     * or throws Exception if no such parameter found or directory/file does not exist.
     *
     * @return array an array of files to be run by PHPUnit
     */
    private static function getTestFiles()
    {
        $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
        $run = null;
        foreach ($argv as $arg) {
            if (preg_match("/^\"?" . self::RUN . "(.+?)\"?$/", $arg, $sub)) {
                $run = $sub[1];
                break;
            }
        }
        if ($run === null) {
            throw new Exception("No argument to run found.");
        }
        if (is_dir($run)) {
            return self::rglob("*[Tt]est.php", $run . DIRECTORY_SEPARATOR);
        } elseif (is_file($run)) {
            return array($run);
        }
        throw new Exception(sprintf("Argument '%s' neither file nor directory.", $run));
    }

    /**
     * Recursive glob().
     *
     * @param  string $pattern the pattern passed to glob(), default is "*"
     * @param  string $path    the path to scan, default is
     * @param  int    $flags   the flags passed to glob()
     * @return array  an array of files in the given path matching the pattern.
     */
    private static function rglob($pattern = '*', $path = '', $flags = 0)
    {
        $paths = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT) or array();
        $files = glob($path . $pattern, $flags) or array();
        foreach ($paths as $path) {
            $files = array_merge($files, self::rglob($pattern, $path, $flags));
        }

        return $files;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
