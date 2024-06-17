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

namespace Koch\Exception;

use Koch\Exception\Renderer\YellowScreenOfDeath;

/**
 * Exception. Sets up a custom Exceptionhandler.
 *
 * @see http://php.net/manual/de/class.exception.php
 * @see http://php.net/manual/de/function.set-exception-handler.php
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
     *
     * @param $exception PHP Exception Objects are valid (Type Hint).
     */
    public function handle(\Exception $exception)
    {
        if ($exception->getCode() > 0) {
            self::fetchExceptionTemplates($exception->getCode());
        }

        echo YellowScreenOfDeath::renderException(
            $exception->getMessage(),
            $exception->getTraceAsString(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTrace()
        );

        // we use our own exception handler here, so PHP returns exit code 0.
        // the execution will stop anyway, but let's return the correct code.
        \Koch\Tools\ApplicationQuit::quit(255);
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
        if (defined('DEVELOPMENT') and DEVELOPMENT === 1) {
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
     * @param int $code The exception code.
     */
    private static function fetchExceptionTemplate($code)
    {
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-' . $code . '.html';

        if (is_file($file)) {
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
     * @param int $code The exception code.
     */
    private static function fetchExceptionDevelopmentTemplate($code)
    {
        // construct filename with code
        $file = APPLICATION_PATH . 'themes/core/exceptions/exception-dev-' . $code . '.html';

        if (is_file($file)) {
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
     * Getter Method for the exception_development_template_content.
     *
     * @return string Representation of $exception_development_template_content
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
     *
     * @return string with Debugtrace String
     */
    public static function formatGetTraceString($string)
    {
        $search  = ['#', '):'];
        $replace = ['<br/><br/>Call #', ')<br/>'];
        $string  = str_replace($search, $replace, $string);
        $string  = ltrim($string, '<br/>');
        unset($search, $replace);

        return $string;
    }
}
