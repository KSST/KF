<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Tools;

/**
 * Application Quit.
 *
 * ExitExpressions are nice. They allow breaking the application control flow at any time.
 * Within regular code ExitExpressions are said to be untestable and therefore they should be avoided.
 * Actually, testing them depends on the testing tool :)
 * If it's stupid enough, then ExitExpressions are untestable.
 *
 * The point of this class is to change nothing about the usage of ExitExpressions,
 * but to provide a central point of "dieing in vain", thereby reducing the number of PHP mess detections.
 */
class ApplicationQuit
{
    /**
     * Exits the application immediately.
     *
     * @param int    $exitCode  ExitCode (defaults to 0 = success).
     * @param string $lastWords Exit Message.
     * @SuppressWarnings(PHPMD)
     */
    public static function quit($exitCode = 0, $lastWords = '')
    {
        if ($lastWords !== '') {
            echo $lastWords;
        }

        exit($exitCode);
    }
}
