<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage Plugins
 */

/**
 * Smarty pagination
 * Displays a pagination Element
 *
 * Examples:
 * <pre>
 * {pagination}
 * </pre>
 *
 * Type:     function<br>
 * Name:     pagination<br>
 * Purpose:  display pagination if needed<br>
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_pagination($params, $smarty)
{
    // check if a alphabet pagination is requested
    if (isset($params['type']) and $params['type'] == 'alphabet') {
        // check if file exists
        if ($smarty->templateExists('pagination-alphabet.tpl') == false) {
            echo 'Pagination Template for alphabet not found.';
        } else { // load the generic pagination template

            return $smarty->fetch('pagination-alphabet.tpl');
        }
    }

    // check if file exists
    if ($smarty->templateExists('pagination-generic.tpl') == false) {
        echo 'Pagination Template not found.';
    } else { // load the generic pagination template

        return $smarty->fetch('pagination-generic.tpl');
    }
}
