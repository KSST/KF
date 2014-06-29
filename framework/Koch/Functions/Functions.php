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

namespace Koch\Functions;

/**
 * Class Library for various static helper Functions.
 */
class Functions
{
    /**
     * @var array This array contains the names of the loaded functions from directory /core/functions.
     */
    public static $alreadyLoaded = array();

    /**
     * Recursive glob
     *
     * @param  string  $pattern
     * @param  int $flags
     * @return type
     */
    public static function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::globRecursive($dir . '/' . basename($pattern), $flags));
        }

        // slash fix
        foreach ($files as $key => $value) {
            $files[$key] = realpath($value);
        }

        return $files;
    }

    public static function getServerLoad()
    {
        if (stristr(PHP_OS, 'win')) {
            $wmi = new COM("Winmgmts://");
            $cpus = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");

            $cpu_num = 0;
            $load_total = 0;

            foreach ($cpus as $cpu) {
                $cpu_num++;
                $load_total += $cpu->loadpercentage;
            }

            $load = round($load_total / $cpu_num);
        } else {
            $sys_load = sys_getloadavg();
            $load = $sys_load[0];
        }

        return (int) $load;
    }

    public static function inString($needle, $haystack, $insensitive = false)
    {
        if ($insensitive === true) {
            return (false !== stristr($haystack, $needle)) ? true : false;
        } else {
            return (false !== strpos($haystack, $needle)) ? true : false;
        }
    }

    /**
     * Checks a string for a certain prefix or adds it, if missing.
     *
     * @param  string $string
     * @param  string $prefix
     * @return string prefixed classname
     */
    public static function ensurePrefixedWith($string, $prefix)
    {
        $pos = null;

        $pos = mb_strpos($string, $prefix);

        if (is_int($pos) and ($pos == 0)) {
            return $string;
        } else {
            return $prefix . $string;
        }
    }

    public function dropNumericKeys(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_int($key) === true) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function issetOrDefault($var, $defaultValue = null)
    {
        return (isset($var) === true) ? $var : $defaultValue;
    }

    public function issetArrayKeyOrDefault(array $array, $key, $defaultValue = null)
    {
        return (isset($array[$key]) === true) ? $array[$key] : $defaultValue;
    }

    /**
     * Transforms a string from underscored_lower_case to Underscored_Upper_Camel_Case.
     *
     * @param  string  $string String in underscored_lower_case format.
     * @return $string String in Upper_Camel_Case.
     */
    public static function toUnderscoredUpperCamelCase($string)
    {
        $upperCamelCase = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($string))));

        return $upperCamelCase;
    }

    /**
     * cut_string_backwards
     *
     * haystack = abc_def
     * needle = _def
     * result = abc
     *
     * In PHP6
     * abc = $string = mb_strstr('abc_def', '_def');
     *
     * @param $haystack string
     * @param $needle string
     * @return string
     */
    public static function cutStringBackwards($haystack, $needle)
    {
        $needle_length = mb_strlen($needle);

        if (($i = mb_strpos($haystack, $needle) !== false)) {
            return mb_substr($haystack, 0, -$needle_length);
        }

        return $haystack;
    }

    /**
     * @param  string  $haystack
     * @param  string  $replace
     * @param  string  $needle
     * @param  int     $times
     * @return $needle
     */
    public static function strReplaceCount($haystack, $replace, $needle, $times)
    {
        $subject_original = $needle;
        $length = mb_strlen($haystack);
        $pos = 0;

        for ($i = 1; $i<=$times; $i++) {
            $pos = mb_strpos($needle, $haystack, $pos);

            if ($pos !== false) {
                $needle = mb_substr($subject_original, 0, $pos);
                $needle .= $replace;
                $needle .= mb_substr($subject_original, $pos + $length);
                $subject_original = $needle;
            } else {
                break;
            }
        }

        return $needle;
    }

    /**
     * Takes a needle and multi-dimensional haystack array and does a search on it's values.
     *
     * @param string $needle   Needle to find
     * @param array  $haystack Haystack to look through
     * @result array Returns the elements that the $string was found in
     *
     * array_values_recursive
     */
    public static function findKeyInArray($needle, array $haystack)
    {
        // take a look for the needle
        if ((isset($haystack[$needle]) === true) or (array_key_exists($needle, $haystack))) {
            // if found, return it
            return $haystack[$needle];
        }

        // dig a little bit deeper in the array structure
        foreach ($haystack as $k => $v) {
            if (is_array($v)) {
                // recursion
                return self::findKeyInArray($needle, $v);
            }
        }

        return false;
    }

    /**
     * array_compare
     *
     * @author  55 dot php at imars dot com
     * @author  dwarven dot co dot uk
     * @link    http://www.php.net/manual/de/function.array-diff-assoc.php#89635
     * @param $array1
     * @param $array2
     */
    public static function arrayCompare($array1, $array2)
    {
        $diff = false;

        // Left-to-right
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2) === false) {
                $diff[0][$key] = $value;
            } elseif (is_array($value)) {
                if (is_array($array2[$key]) === false) {
                    $diff[0][$key] = $value;
                    $diff[1][$key] = $array2[$key];
                } else {
                    $new = self::array_compare($value, $array2[$key]);

                    if ($new !== false) {
                        if($new[0] !== null)
                            $diff[0][$key] = $new[0];
                        if($new[1] !== null)
                            $diff[1][$key] = $new[1];
                    }
                }
            } elseif ($array2[$key] !== $value) {
                $diff[0][$key] = $value;
                $diff[1][$key] = $array2[$key];
            }
        }

        // Right-to-left
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $array1) === false) {
                $diff[1][$key] = $value;
            }

            /**
             * No direct comparsion because matching keys were compared in the
             * left-to-right loop earlier, recursively.
             */
        }

        return $diff;
    }

    /**
     * Combines two arrays by using $keyArray as key providing array
     * an $valueArray as value providing array.
     * In case the valueArray is greater than the keyArray,
     * the keyArray determines the maximum number of values returned.
     * In case the valueArray is smaller than the keyArray,
     * those keys are returned for which values exist.
     *
     * @example
     * $keys = array('mod', 'sub', 'action', 'id');
     * $values = array('news', 'admin');
     * $combined = self::array_unequal_combine($keys, $values);
     * Results in: array('mod'=>'news', 'sub'=>'admin');
     *
     * @param  array $keyArray
     * @param  array $valueArray
     * @return array Combined Array
     */
    public static function arrayUnequalCombine($keyArray, $valueArray)
    {
        $returnArray = array();
        $key = '';
        $index = 0;

        // more keys than values, reduce keys array
        while (count($keyArray) > count($valueArray)) {
            array_pop($keyArray);
        }

        // @todo more values than keys ?
        // add pseudo keys a la "key-0"

        foreach ($keyArray as $key) {
            if ($valueArray[$index] !== null) {
                // index is used, then incremented for the next turn in foreach (post-increment-operator)
                $returnArray[$key] = $valueArray[$index++];
            }
        }

        return $returnArray;
    }

    /**
     * Remaps multi-dim array (k1=>v1, k2=>v2, k(n) => v(n)) to the values of key1=>key2 (v1 => v2).
     * The array might have several keys, so you might map value of key2 to value of key5 ;)
     * Simple, but impressive!
     *
     * @param  type $array
     * @param  type $map_value_of_key1
     * @param  type $to_value_of_key2
     * @return type array
     */
    public static function mapArrayKeysToValues($array, $map_value_of_key1, $to_value_of_key2)
    {
        $new_array = array();
        foreach ($array as $inner_array) {
           $new_array[$inner_array[$map_value_of_key1]] = $inner_array[$to_value_of_key2];
        }

        return $new_array;
    }

    /**
     * flatten multi-dimensional array
     *
     * @param  array $array
     * @return array
     */
    public static function arrayFlatten(array $array)
    {
        $flatened_array = array();
        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array)) as $value) {
            $flatened_array[] = $value;
        }

        return $flatened_array;
    }

    /**
     * distanceOfTimeInWords
     *
     * @author: anon
     * @link: http://www.php.net/manual/de/function.time.php#85481
     *
     * @param  int $fromTime starttime
     * @param $toTime endtime
     * @param $showLessThanAMinute boolean
     * @return string
     */
    public static function distanceOfTimeInWords($fromTime, $toTime = 0, $showLessThanAMinute = false)
    {
        $distanceInSeconds = round(abs($toTime - $fromTime));
        $distanceInMinutes = round($distanceInSeconds / 60);

        if ($distanceInMinutes <= 1) {
            if ($showLessThanAMinute == false) {
                return ($distanceInMinutes == 0) ? 'less than a minute' : '1 minute';
            } else {
                if ($distanceInSeconds < 5) {
                    return 'less than 5 seconds';
                }
                if ($distanceInSeconds < 10) {
                    return 'less than 10 seconds';
                }
                if ($distanceInSeconds < 20) {
                    return 'less than 20 seconds';
                }
                if ($distanceInSeconds < 40) {
                    return 'about half a minute';
                }
                if ($distanceInSeconds < 60) {
                    return 'less than a minute';
                }

                return '1 minute';
            }
        }
        if ($distanceInMinutes < 45) {
            return $distanceInMinutes . ' minutes';
        }
        if ($distanceInMinutes < 90) {
            return 'about 1 hour';
        }
        if ($distanceInMinutes < 1440) {
            return 'about ' . round(floatval($distanceInMinutes) / 60.0) . ' hours';
        }
        if ($distanceInMinutes < 2880) {
            return '1 day';
        }
        if ($distanceInMinutes < 43200) {
            return 'about ' . round(floatval($distanceInMinutes) / 1440) . ' days';
        }
        if ($distanceInMinutes < 86400) {
            return 'about 1 month';
        }
        if ($distanceInMinutes < 525600) {
            return round(floatval($distanceInMinutes) / 43200) . ' months';
        }
        if ($distanceInMinutes < 1051199) {
            return 'about 1 year';
        }

        return 'over ' . round(floatval($distanceInMinutes) / 525600) . ' years';
    }

    /**
     * Performs a dateToWord transformation via gettext.
     * uses idate() to format a local time/date as integer and gettext functions _n(), _t()
     * @see http://www.php.net/idate
     *
     * @param  string $from
     * @param  string $now
     * @return string Word representation of
     */
    public static function dateToWord($from, $now = null)
    {
        if ($now === null) {
            $now = time();
        }

        $between = $now - $from;

        if ($between < 86400 and idate('d', $from) == idate('d', $now)) {

            if ($between < 3600 and idate('H', $from) == idate('H', $now)) {

                if ($between < 60 and idate('i', $from) == idate('i', $now)) {
                    $second = idate('s', $now) - idate('s', $from);

                    return sprintf(_n('%d', '%d', $second), $second);
                }

                $min = idate('i', $now) - idate('i', $from);

                return sprintf(_n('%d', '%d', $min), $min);
            }

            $hour = idate('H', $now) - idate('H', $from);

            return sprintf(_n('%d', '%d', $hour), $hour);
        }

        if ($between < 172800 and ( idate('z', $from) + 1 == idate('z', $now) or idate('z', $from) > 2 + idate('z', $now))) {
            return _t('.. %s', date('H:i', $from));
        }

        if ($between < 604800 and idate('W', $from) == idate('W', $now)) {
            $day = intval($between / (3600 * 24));

            return sprintf(_n('...', '...', $day), $day);
        }

        if ($between < 31622400 and idate('Y', $from) == idate('Y', $now)) {
            return date(_t('...'), $from);
        }

        return date(_t('...'), $from);
    }

    /**
     * Get the variable name as string
     *
     * @author http://us2.php.net/manual/en/language.variables.php#76245
     * @param $var variable as reference
     * @param $scope scope
     * @return string
     */
    public static function vname($var, $scope = false, $prefix = 'unique', $suffix = 'value')
    {
        $values = '';

        if ($scope === true) {
            $values = $scope;
        }

        $old = $var;
        $var = $new = $prefix . rand() . $suffix;
        $vname = false;

        foreach ($values as $key => $val) {
            if ($val === $new) {
                $vname = $key;
            }
        }
        $var = $old;

        return $vname;
    }

    /**
     * format_seconds_to_shortstring
     *
     * @param $seconds int
     * @return string Ouput: 4D 10:12:20
     */
    public static function formatSecondsToShortstring($seconds = 0)
    {
        $time = '';
        if ($seconds !== null) {
            $time = sprintf('%dD %02d:%02d:%02dh', $seconds / 60 / 60 / 24, ($seconds / 60 / 60) % 24, ($seconds / 60) % 60, $seconds % 60);
        } else {
            return '00:00:00';
        }

        return $time;
    }

    /**
     * Remove comments prefilter
     *
     * @param $html A String with HTML Comments.
     * @return string $html String without Comments.
     */
    public function removeCommentsFromTemplate($html)
    {
        return preg_replace('/<!--.*-->/U', '', $html);
    }

    /**
     * @param string $string
     */
    public static function shortenString($string, $maxlength = 50, $append_string = '[...]')
    {
        // already way too short...
        if (mb_strlen($string) < $maxlength) {
            return $string;
        }

        // ok, lets shorten
        if (mb_strlen($string) > $maxlength) {
            /**
             * do not short the string, when maxlength would split a word!
             * that would make things unreadable.
             * so search for the next space after the requested maxlength.
             */
            $next_space_after_maxlength = mb_strpos($string, ' ', $maxlength);

            $shortened_string = mb_substr($string, 0, $next_space_after_maxlength);

            return $shortened_string . $append_string;
        }
    }

    /**
     * Converts a UTF8-string into HTML entities
     *
     * When using UTF-8 as a charset you want to convert multi-byte characters.
     * This function takes multi-byte characters up to level 4 into account.
     * Htmlentities will only convert 1-byte and 2-byte characters.
     * Use this function if you want to convert 3-byte and 4-byte characters also.
     *
     * @author silverbeat gmx  at
     * @link http://www.php.net/manual/de/function.htmlentities.php#96648
     *
     * @param $utf8 string The UTF8-string to convert
     * @param $encodeTags booloean TRUE will convert "<" to "&lt;", Default = false
     * @return string the converted HTML-string
     */
    public static function utf8ToHtml($utf8, $encodeTags = false)
    {
        include_once __DIR__ . '/Pool/UTF8_to_HTML.php';

        // calling the loaded function
        return UTF8_to_HTML($utf8, $encodeTags);
    }

    /**
     * The Magic Call __callStatic() is triggered when invoking inaccessible methods in a static context.
     * Method overloading.
     * Available from PHP 5.3 onwards.
     *
     * @param $name string The $name argument is the name of the method being called.
     * @param $arguments arra The $arguments argument is an enumerated array containing the parameters passed to the $name'ed method.
     */
    public static function __callStatic($method, $arguments)
    {
        // Debug message for Method Overloading
        // Making it easier to see which static method is called magically
        //\Koch\Debug\Debug::firebug('DEBUG (Overloading): Calling static method "'.$method.'" '. implode(', ', $arguments). "\n");
        // construct the filename of the command
        $filename = __DIR__  . '/Pool/' . $method . '.php';

        // check if name is valid
        if (is_file($filename) === true and is_readable($filename) === true) {
            // dynamically include the command
            include_once $filename;

            return call_user_func_array($method, $arguments);
        } else {
            throw new \RuntimeException(
                sprintf('Koch Framework Function not found: "%s".', $filename)
            );
        }
    }

    /**
     * The Magic Call __call() is triggered when invoking inaccessible methods in an object context.
     * Method overloading.
     *
     * This method takes care of loading the function command files.
     *
     * This means that a currently non-existing methods or properties of this class are dynamically "created".
     * Overloading methods are always in the "public" scope.
     *
     * @param $name string The $name argument is the name of the method being called.
     * @param $arguments array The $arguments  argument is an enumerated array containing the parameters passed to the $name'ed method.
     */
    public function __call($method, $arguments)
    {
        // Because value of $name is case sensitive, its forced to be lowercase.
        $method = mb_strtolower($method);

        // Debug message for Method Overloading
        // Making it easier to see which method is called magically
        // \Koch\Debug\Debug::fbg('DEBUG (Overloading): Calling object method "'.$method.'" '. implode(', ', $arguments). "\n");
        // construct the filename of the command
        $filename = __DIR__  . '/pool/' . $method . '.php';

        // check if name is valid
        if (is_file($filename) === true and is_readable($filename) === true) {
            // dynamically include the command
            include_once $filename;

            return call_user_func_array($method, $arguments);
        } else {
            throw new \RuntimeException(
                sprintf('Koch Framework Function not found: "%s".', $filename)
            );
        }
    }

}
