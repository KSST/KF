<?php

/**
 * Clansuite - just an eSports CMS
 * Jens-André Koch © 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * *
 */

// setup env(error/date/paths)
require_once 'bootstrap.php';

/**
 * Setup Simpletest
 */
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/test_case.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/web_tester.php';

// hmm, we need to load this, to get rid of the "No runnable test cases in runAlltest" error
#require_once 'simpletest/autorun.php';

/**
 * Setup our Testsuite and Reporter
 */
require_once 'reporter.php';
require_once 'testsuite.php';
require_once 'unittester.php';

// start CodeCoverage
if (PERFORM_CODECOVERAGE == true) {
    require_once 'codecoverage.php';
    Clansuite_CodeCoverage::start();
}

// Tests -> instantiate Clansuite Testsuite
$testsuite = new ClansuiteTestsuite();
$success = false;

// Tests -> determine, if we are in commandline mode, then output pure text
if (TextReporter::inCli()) {

    // fetch reporter
    #require_once 'simpletest/extensions/colortext_reporter.php';
    require_once 'simpletest/extensions/junit_xml_reporter.php';
    ob_start();
    #$reporter = new TextReporter();
    #$reporter = new ColorTextReporter();
    $reporter = new JUnitXmlReporter();

    // hand reporter to the testsuite and run it
    $success = $testsuite->run($reporter);

    // write test results to xml file
    file_put_contents(__DIR__ . '/test-results.xml', ob_get_contents());
    ob_end_clean();

    // do not let the tests fail, the fail status is evaluated via xml file
    $success = true;
} else {
    // display nice html report
    $success = $testsuite->run(new Reporter);
}

// stop CodeCoverage
if (PERFORM_CODECOVERAGE == true) {
    Clansuite_CodeCoverage::stop();
    Clansuite_CodeCoverage::getReport();
}

// Tests -> exit with status
if (false === $success) {
    // Exit with error code to let the build fail, when the test is unsuccessfull.
    exit(1);
}
