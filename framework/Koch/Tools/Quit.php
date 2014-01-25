<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
     * @param int $exitCode ExitCode (defaults to 0 = success).
     * @param string $lastWords Exit Message.
     */
    static function quit($exitCode = 0, $lastWords = '')
    {
        if($lastWords != '') {
            echo $lastWords;
        }
        
        exit($exitCode);
    }
}
