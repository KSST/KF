<?php

/**
 * Clansuite - just an eSports CMS
 * Jens-Andr� Koch � 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * *
 */

class Clansuite_CodeCoverage
{
    public static $coverage;

    /**
    * Starts the Code Coverage.
    */
    public static function start()
    {
        /**
        * Simpletest Code Coverage depends on xdebug.
        *
        * Ensure that the xdebug extension is loaded.
        */
        if (false === extension_loaded('xdebug')) {
            die('Code Coverage needs Xdebug extension. Not loaded!');
        }

        if (false === function_exists("xdebug_start_code_coverage")) {
            die('Code Coverage needs the method xdebug_start_code_coverage. Not found!');
        }

        /**
        * Simpletest Code Coverage depends on sqlite.
        *
        * Ensure that the sqlite extension is loaded.
        */
        /*if (false === class_exists('SQLiteDatabase')) {
            echo 'Code Coverage needs the php extension SQLITE. Not loaded!';
        }*/

        /**
        * Setup Simpletest Code Coverage.
        */
        require_once 'simpletest/extensions/coverage/coverage.php';

        $coverage = new CodeCoverage();
        $coverage->log = 'coverage.sqlite';
        $coverage->root = dirname(__DIR__);
        $coverage->includes[] = '.*\.php$';
        $coverage->excludes[] = 'simpletest';
        $coverage->excludes[] = 'tests';
        $coverage->excludes[] = 'libraries';
        $coverage->excludes[] = 'vendor';
        $coverage->excludes[] = 'vendors';
        $coverage->excludes[] = 'coverage-report';
        $coverage->excludes[] = 'sweety';
        $coverage->excludes[] = './.*.php';
        $coverage->maxDirectoryDepth = 1;
        $coverage->resetLog();
        $coverage->writeSettings();

        /**
        * Finally: let's start the Code Coverage.
        */
        $coverage->startCoverage();
        #echo 'Code Coverage started...';

        self::$coverage = $coverage;
    }

    /**
    * Stops the Code Coverage.
    */
    public static function stop()
    {
        self::$coverage->writeUntouched();
        self::$coverage->stopCoverage();
        #echo 'Code Coverage stopped!';
    }

    /**
    * Generates the Code Coverage Report.
    */
    public static function getReport()
    {
        require_once 'simpletest/extensions/coverage/coverage_reporter.php';

        $handler = new CoverageDataHandler(self::$coverage->log);
        $report = new CoverageReporter();
        $report->reportDir = 'coverage-report';
        $report->title = 'Clansuite Coverage Report';
        $report->coverage = $handler->read();
        $report->untouched = $handler->readUntouchedFiles();
        $report->generate();
    }
}
