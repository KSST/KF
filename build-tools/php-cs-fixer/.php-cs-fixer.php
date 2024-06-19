<?php

/**
 * php-cs-fixer - configuration file
 */

use Symfony\CS\FixerInterface;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->ignoreVCS(true)
    ->notName('.php_cs')
    ->notName('travis-setup.php')
    ->notName('php-cs-fixer.report.txt')
    ->notName('AllTests.php')
    ->notName('composer.*')
    ->notName('*.phar')
    ->notName('*.ico')
    ->notName('*.ttf')
    ->notName('*.gif')
    ->notName('*.swf')
    ->notName('*.jpg')
    ->notName('*.png')
    ->notName('*.exe')
    ->notName('*classmap.php')
    ->notName('Utf8FallbackFunctions.php') // bug in php-cs-fixer, adds "public" to global functions
    ->notName('MbstringWrapper.php') // bug in php-cs-fixer, adds "public" to global functions
    ->exclude('vendor')
    ->exclude('libraries')
    ->exclude('nbproject') // netbeans project files
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->finder($finder)
    // use SYMFONY_LEVEL:
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    // and extra fixers:
    ->fixers(array(
        'align_equals',
        'align_double_arrow',
        'concat_with_spaces',
        'ordered_use',
        'strict',
        'strict_param',
        'short_array_syntax'
    ));