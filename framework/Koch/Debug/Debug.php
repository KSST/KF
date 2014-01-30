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

namespace Koch\Debug;

/**
 * Debug
 *
 * This class initializes debugging helpers like xdebug, the doctrine profiler,
 * firebug and printR at system start-up and displays debug
 * and runtime-informations on demand or at application shutdown.
 */
class Debug
{
    /**
     * This is an enhanced version of the native php function print_r().
     *
     * @param mixed/array/object $var  Array or Object as Variable to display
     * @param bool               $exit Stop execution after dump? Default is true (stops).
     * @returns Returns a better structured display of an array/object as native print_r().
     */
    public static function printR($var)
    {
        // this will handle more than one parameter
        if (func_num_args() > 1) {
            $vars = func_get_args();
        }

        $backtrace_array = array();
        $backtrace_array = debug_backtrace();
        $trace = array_shift($backtrace_array);
        $file = file($trace['file']);
        $trace_line = $file[$trace['line']-1];

        echo '<pre>';
        echo '<b>Debugging ';
        echo '<font color=red>'.basename($trace['file']).'</font>';
        echo ' on line <font color=red>'.$trace['line'].'</font></b>:' . "\n";
        echo "<div style='background: #f5f5f5; padding: 0.2em 0em;'>".htmlspecialchars($trace_line).'</div>' . "\n";

        echo '<b>Type</b>: ' . gettype($var) . "\n"; // uhhh.. gettype is slow like hell

        // handle more than one parameter
        foreach ($vars as $var) {

            if (is_string($var) === true) {
                echo '<b>Length</b>: ' . strlen($var) . "\n";
            }

            if (is_array($var) === true) {
                echo '<b>Length</b>: ' . count($var) . "\n";
            }

            echo '<b>Value</b>: ';

            if ($var === true) {
                echo '<font color=green><b>true</b></font>';
            } elseif ($var === false) {
                echo '<font color=red><b>false</b></font>';
            } elseif ($var === null) {
                echo '<font color=red><b>null</b></font>';
            } elseif ($var === 0) {
                echo '0';
            } elseif (is_string($var) and strlen($var) == '0') {
                echo '<font color=green>*EMPTY STRING*</font>';
            } elseif (is_string($var)) {
                echo htmlspecialchars($var);
            } else {
                $print_r = print_r($var, true);
                // str_contains < or >
                if ((strstr($print_r, '<') !== false) or (strstr($print_r, '>') !== false)) {
                    $print_r = htmlspecialchars($print_r);
                }
                echo $print_r;
            }

        }

        echo '</pre>';

        // save session before exit
        if ((bool) session_id()) {
            session_write_close();
        }

        // do not exit, if we are inside a test run
        if (defined('UNIT_TEST_RUN') === false or UNIT_TEST_RUN === false) {
            \Koch\Tools\ApplicationQuit::quit();
        }
    }

    /**
     * Displays the content of a variable with var_dump.
     * The content gets escaping and pre tags are applied for better readability.
     *
     * @param mixed $var  The variable to debug.
     * @param bool  $exit Stop execution after dump? Default is true (stops).
     */
    public static function dump($var, $exit = true)
    {
        // var_dump the content into a buffer and store it to variable
        ob_start();
        var_dump($var);
        $var_dump = ob_get_clean();

        /**
         * if xdebug is on and overloaded the var_dump function,
         * then the output is already properly escaped and prepared for direct output.
         * if xdebug is off, we need to apply escaping ourself.
         * html pre tags are applied to structure the display a bit more.
         */
        if (false === extension_loaded('xdebug')) {
            $var_dump = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $var_dump);
            $var_dump = '<pre>' . htmlspecialchars($var_dump, ENT_QUOTES, 'UTF-8') . '</pre>';
        }

        // display where this debug statement
        echo self::getOriginOfDebugCall();

        // output the content of the buffer
        echo $var_dump;

        // do not exit, if we are inside a test run
        if (defined('UNIT_TEST_RUN') === false or UNIT_TEST_RUN === false) {
            if ($exit === true) {
                \Koch\Tools\ApplicationQuit::quit();
            }
        }
    }

    /**
     * Debug logs the output of $var to the firebug console in your browser.
     *
     * @param  mixed   $var The variable to debug.
     * @param $logmethod The firebug method to call for logging (log,info,warn, error). Defaults to "log".
     * @return FirePHP object.
     */
    public static function firebug($var, $logmethod = 'log')
    {
        // @codeCoverageIgnoreStart
        // We don't need to test vendor library functionality.
        // @see FirePHPCore_FirePHPTest

        $firephp = \FirePHP::getInstance(true);

        /**
         * Adds an info message about the position of the firebug call (origin).
         * This is very helpful if you spread Debug::firebug() calls all over your code.
         */
        $firephp->info(self::getOriginOfDebugCall());

        $firephp->{$logmethod}($var);

        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns the position of a call.
     *
     * This is used to determine the origin of the debug call.
     * Its mostly used in combination with several debug calls,
     * like \Koch\Debug\Debug::firebug() or \Koch\Debug\Debug::printR()
     * which are enhanced debug displays.
     *
     * It is a very helpful reminder to find and remove debug calls,
     * which you spread all over your code while tracking down a bug,
     * but forgot the trace path or where exactly they are.
     *
     * If you have a multitude of debug calls, debug breadcrumbs or toc
     * would be good ;) But that's another story...
     *
     * The default level is 2 (0,1,2), because we have to skip
     * the 3 calls to dump() and getWhereDebugWasCalled().
     *
     * @param  int    $level Default 1.
     * @return string Message with origin of the debug call.
     */
    public static function getOriginOfDebugCall($level = 1)
    {
        $trace  = array();
        $file = $line = $function = $class = $object = $trace_line = '';

        // Get the backtrace and the caller information.
        $trace = debug_backtrace();
        $file = $trace[$level]['file'];
        $line = $trace[$level]['line'];
        #$function = $trace[$level]['function'];
        #$class = $trace[$level]['class'];

        /**
         * Get the file, to show the exact origin of the debug call.
         * The line with the call, is one line above.
         */
        $file_content = file($file);
        $origin_of_call = trim($file_content[ $trace[$level]['line']-1 ]);

        // do not use HTML tags on CLI
        if (php_sapi_name() === 'cli') {
            if(empty($_SERVER['REMOTE_ADDR'])) {
                $format = 'Debugging %s on line %s: %s'. "\n";
            } else {
                $format = '<pre>';
                $format .= '<b>Debugging <font color="red">%s</font> on line <font color="red">%s</font>:</b>' . "\n";
                $format .= '<div style="background: #f5f5f5; padding: 0.2em 0em;">%s</div></pre>';
            }
        }

        echo sprintf($format, basename($file), $line, htmlspecialchars($origin_of_call));
    }

    /**
     * The method
     * - lists all currently included and required files
     * - counts all includes files
     * - calculates the total size (combined filesize) of all inclusions.
     */
    public static function getIncludedFiles($returnArray = false)
    {
        // init vars
        $includedFiles = $files = $result = array();
        $totalSize = 0;

        // fetch all included files
        $files = get_included_files();

        // loop over all included files and sum up filesize
        foreach ($files as $file) {

            // if system under test, skip virtual file system files,
            // as they might be already deleted by tearDown() methods.
            if (defined('UNIT_TEST_RUN') === true or UNIT_TEST_RUN === true) {
                if (stripos($file, "vfs:/") !== false) {
                    continue;
                }
            }

            $size = filesize($file);
            $includedFiles[] = array('name' => $file, 'size' => $size);
            $totalSize += $size;
        }

        $result = array(
            'count' => count($files),
            'size' => \Koch\Functions\Functions::getSize($totalSize),
            'files' => $includedFiles
        );

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Lists all user defined constants (Application Constants).
     */
    public static function getApplicationConstants($returnArray = false)
    {
        $constants = get_defined_constants(true);
        $result = $constants['user'];

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Displayes the debug backtrace.
     *
     * @param int Limit the number of stack frames returned. Returns all stack frames by default (limit=0).
     * @param bool
     */
    public static function getBacktrace($limit = 0, $returnArray = false)
    {
        $result = debug_backtrace($limit);

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Returns an array with the name of the defined interfaces.
     */
    public static function getInterfaces($returnArray = false)
    {
        $result = get_declared_interfaces();

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Returns an array with the name of the defined classes.
     */
    public static function getClasses($returnArray = false)
    {
        $result = get_declared_classes();

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Returns an array with the name of the defined classes.
     */
    public static function getFunctions($returnArray = false)
    {
        $result = get_defined_functions();

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Lists all php extensions.
     */
    public static function getExtensions($returnArray = false)
    {
        $result = get_loaded_extensions();

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Lists all php.ini settings.
     */
    public static function getPhpIni($returnArray = false)
    {
        $result = parse_ini_file(get_cfg_var('cfg_file_path'), true);

        return ($returnArray === true) ? $result : self::printR($result);
    }

    /**
     * Lists all available wrappers
     */
    public static function getWrappers($returnArray = false)
    {
        $result = array();

        $wrappers = stream_get_wrappers();

        $result['openssl'] = (extension_loaded('openssl')) ? 'yes' : 'no';
        $result['http'] = in_array('http', $wrappers) ? 'yes' : 'no';
        $result['https'] = in_array('https', $wrappers) ? 'yes' : 'no';
        $result['all'] = $wrappers;

        return ($returnArray === true) ? $result : self::printR($result);
    }
}
