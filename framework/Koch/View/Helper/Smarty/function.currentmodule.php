<?php
/**
 * Smarty plugin.
 */

/**
 * Smarty Currentmodule.
 *
 * Displays the name of the current module
 *
 * Examples:
 * <pre>
 * {pagination}
 * </pre>
 *
 * Type:     function<br>
 * Name:     currentmodule<br>
 * Purpose:  display name of current module<br>
 *
 * @return string
 */
function Smarty_function_currentmodule()
{
    return \Koch\Router\TargetRoute::getModule();
}
