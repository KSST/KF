<?php
/**
 * Smarty plugin
 */

/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     duration<br>
 * Date:     Oct 07, 2008
 * Purpose:  show format_seconds_to_shortstring from current timestamp in seconds
 * Input:
 *
 * Example:  {$seconds|formatseconds}
 * @param string
 * @return string
 */
function smarty_modifier_formatseconds($seconds)
{
    return \Koch\Functions\Functions::formatSecondsToShortstring($seconds);
}
