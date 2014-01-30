<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * This file is part of https://github.com/KSST/KF
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * */

/**
 * Class provides MB_STRING Wrapper Methods.
 *
 * Koch Frameworkrelies on mb_string functions.
 * If the mbstring extension is not loaded, the mb_string functions are not available.
 * Here we define some mbstring wrapper functions, which use custom utf8 methods internally
 * and rebuild the mbstring behaviour. This means that calls to mbstring functions throughout
 * the sourcecode are being replaced by our own UTF8 functions.
 *
 * The following functions are declared for global usage:
 *
 *  mb_convert_encoding
 *  mb_detect_encoding
 *  mb_stripos
 *  mb_stristr
 *  mb_strlen
 *  mb_strpos
 *  mb_strrchr
 *  mb_strrpos
 *  mb_strstr
 *  mb_strtolower
 *  mb_strtoupper
 *  mb_substr
 *  mb_substr_count
 *
 *  Note: The function_exists() checks are needed to prevent redeclaration
 *        errors, which would occur during linting.
 */

/**
 * mb_convert_encoding
 */
if (!function_exists('mb_convert_encoding')) {

    function mb_convert_encoding($str, $to_encoding, $from_encoding = null)
    {
        if (null === $from_encoding) {
            return utf8_convert_encoding($str, $to_encoding);
        } else {
            return utf8_convert_encoding($str, $to_encoding, $from_encoding);
        }
    }
}

/**
 * mb_detect_encoding
 */
if (!function_exists('mb_detect_encoding')) {

    function mb_detect_encoding($str)
    {
        return utf8_detect_encoding($str);
    }
}

/**
 * mb_stripos
 */
if (!function_exists('mb_stripos')) {

    function mb_stripos($haystack, $needle, $offset = null)
    {
        if (null === $offset) {
            return stripos($haystack, $needle);
        } else {
            return stripos($haystack, $needle, $offset);
        }
    }
}

/**
 * mb_stristr
 */
if (!function_exists('mb_stristr')) {

    function mb_stristr($haystack, $needle)
    {
        return stristr($haystack, $needle);
    }
}

/**
 * mb_strlen
 */
if (!function_exists('mb_strlen')) {

    function mb_strlen($str, $encoding = '')
    {
        return utf8_strlen($str);
    }
}

/**
 * mb_strpos
 */
if (!function_exists('mb_strpos')) {

    function mb_strpos($haystack, $needle, $offset = null)
    {
        if (null === $offset) {
            return utf8_strpos($haystack, $needle);
        } else {
            return utf8_strpos($haystack, $needle, $offset);
        }
    }
}
/**
 * mb_strrchr
 */
if (!function_exists('mb_strrchr')) {

    function mb_strrchr($haystack, $needle)
    {
        return utf8_strrchr($haystack, $needle);
    }
}

/**
 * mb_strrpos
 */
if (!function_exists('mb_strrpos')) {

    function mb_strrpos($haystack, $needle)
    {
        return utf8_strrpos($haystack, $needle);
    }
}

/**
 * mb_strstr
 */
if (!function_exists('mb_strstr')) {

    function mb_strstr($haystack, $needle)
    {
        return utf8_strstr($haystack, $needle);
    }
}

/**
 * mb_strtolower
 */
if (!function_exists('mb_strtolower')) {

    function mb_strtolower($str)
    {
        return utf8_strtolower($str);
    }
}

/**
 * mb_strtoupper
 */
if (!function_exists('mb_strtoupper')) {

    function mb_strtoupper($str)
    {
        return utf8_strtoupper($str);
    }
}

/**
 * mb_substr
 */
if (!function_exists('mb_substr')) {

    function mb_substr($str, $start, $length = null)
    {
        if (null === $length) {
            return utf8_substr($str, $start);
        } else {
            return utf8_substr($str, $start, $length);
        }
    }
}

/**
 * mb_substr_count
 */
if (!function_exists('mb_substr_count')) {

    function mb_substr_count($haystack, $needle, $offset = null)
    {
        if (null === $offset) {
            return substr_count($haystack, $needle);
        } else {
            return substr_count($haystack, $needle, $offset);
        }
    }
}
