<?php
/**
 * Smarty plugin
 */

/**
 * smarty_function_dbexectime
 */
function Smarty_function_dbexectime($params, $smarty)
{
    if (DEBUG == 1) {
        echo Koch_Doctrine2::getExecTime();
    } else {
        echo 'Disabled';
    }
}
