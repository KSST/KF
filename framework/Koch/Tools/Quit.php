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

namespace Koch\Tools;

/**
 * Quit 
 * 
 * ExitExpressions are nice. They allow breaking the application control flow at any time.
 * Within regular code ExitExpressions are said to be untestable and therefore they should be avoided.
 * Actually, testing them depends on the testing tool :) 
 * If it's stupid enough, then ExitExpressions are untestable.
 * 
 * The point of this class is to change nothing about the usage of ExitExpressions,
 * but to provide a central point of "dieing in vain", thereby reducing the number of PHP mess detections.
 */
class Quit
{
    /**
     * Exits the application immediately.
     * 
     * @param string $lastWords
     * @param int $exitCode ExitCode (0=success)
     */
    static function quit($lastWords, $exitCode = 0)
    {
        echo $lastWords;
        exit($exitCode);
    }
}
