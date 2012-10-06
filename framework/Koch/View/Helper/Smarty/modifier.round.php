﻿<?php
/**
 * Smarty plugin
 * @package Clansuite
 * @subpackage smarty_plugins
 */

/**
 * Smarty round modifier plugin
 *
 * Type:     modifier<br>
 * Name:     round<br>
 * Purpose:  round a numeric string to a given decimal point
 * @param string
 * @param int
 * @return string
 */
function Smarty_modifier_round($float, $precision = 0)
{
    return round((float) $float, $precision);
}
