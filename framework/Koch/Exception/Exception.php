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
 *
 */

namespace Koch\Exception;

use Koch\Exception\Renderer\YellowScreenOfDeath;

/**
 * Koch Framework - Exception. Sets up a custom Exceptionhandler.
 *
 * @see http://php.net/manual/de/class.exception.php
 * @see http://php.net/manual/de/function.set-exception-handler.php
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Exceptionhandler
 */
class Exception extends \Exception
{
    /**
     * @var string HTML Representation of the Exception Template
     */
    private static $exceptionTemplate = '';

    /**
     * @var string HTML Representation of the Exception Development (RAD) Template
     */
    private static $developmentTemplate = '';

    /**
     * Exception Handler Callback
     * Rethrows uncatched Exceptions in our presentation style.
     *
     * @see http://php.net/manual/de/function.set-exception-handler.php
     * @param $exception PHP Exception Objects are valid (Type Hint).
     */
    public function handle(\Exception $exception)
    {
        if ($exception->getCode() > 0) {
            self::fetchExceptionTemplates($exception->getCode());
        }

        /**
         * @todo
         * 1. catch Smarty "Template Syntax" Errors
         * 2. provide link to templateeditor (file:line) to fix the error
         */
        /*$smartyTemplateError = (false !== stristr($exception->getMessage(), 'Syntax Error in template')) ? true : false;
        if ($smartyTemplateError === true) {
            throw new SmartyTemplateException($exception);
        }*/

        /**
         * @todo
         * 1. catch Smarty "Unable to load template file" Errors
         * 2. provide link to templatefilemanager (module:file)
         */

        echo \Koch\Exception\Renderer\YellowScreenOfDeath::renderException(
            $exception->getMessage(),
            $exception->getTraceAsString(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTrace()
        );
    }

    /**
     * Fetches the normal and rapid development templates for exceptions and sets them to class.
     * Callable via self::getExceptionTemplate() and self::getExceptionDevelopmentTemplate($placeholders).
     *
     * @param int $code Exception Code
     */
    private static function fetchExceptionTemplates($code)
    {
        // normal exception template
        self::fetchExceptionTemplate($code);

        // development template
        if (defined('DEVELOPMENT') and DEVELOPMENT == 1) {
            self::fetchExceptionDevelopmentTemplate($code);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     *
     * You find the Exception Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new \Koch\Exception\Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionTemplate($code)
    {
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-' . $code . '.html';

        if (is_file($file) === true) {
            self::$exceptionTemplate = file_get_contents($file);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     * This is for rapid development purposes.
     *
     * You find the Exception Development Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-dev-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new \Koch\Exception\Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-dev-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionDevelopmentTemplate($code)
    {
        // construct filename with code
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-dev-' . $code . '.html';

        if (is_file($file) === true) {
            self::$developmentTemplate = file_get_contents($file);

            define('RAPIDDEVTPL', true);
        }
        /*
        else {
           // @todo propose to create a new rapid development template via tpleditor
        }
        */
    }

    /**
     * Getter Method for the exception_development_template_content
     *
     * @return HTML Representation of $exception_development_template_content
     */
    public static function getExceptionDevelopmentTemplate($placeholders)
    {
        $content = self::$developmentTemplate;

        if ($placeholders['modulename'] !== null) {
            $content = str_replace('{$modulename}', $placeholders['modulename'], $content);
        }

        if ($placeholders['classname'] !== null) {
            $content = str_replace('{$classname}', $placeholders['classname'], $content);
        }

        if ($placeholders['actionname'] !== null) {
            $content = str_replace('{$actionname}', $placeholders['actionname'], $content);
        }

        return $content;
    }

    /**
     * Formats the debugtrace ($this->string) by applying linebreaks.
     *
     * @param $string The debug-trace string to format.
     * @return HTML with Debugtrace String
     */
    public static function formatGetTraceString($string)
    {
        $search  = array('#', '):');
        $replace = array('<br/><br/>Call #', ')<br/>');
        $string  = str_replace($search, $replace, $string);
        $string  = ltrim($string, '<br/>');
        unset($search, $replace);

        return $string;
    }
}
