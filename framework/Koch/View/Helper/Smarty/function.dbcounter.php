<?php
/**
 * Smarty plugin
 */

/**
 * smarty_function_dbexectime
 */
function Smarty_function_dbcounter($params, $smarty)
{
    if (DEBUG == 1) {
        /**
         * The call to this viehelper "dbcounter" is performed inside the view.
         * So the Query for closing the session is missing, because it's
         * performed on shutdown of the application.
         * We simply add one Query..
         */
        echo \Koch\Doctrine\Doctrine::getNumberOfQueries() + 1;
    } else {
        echo 'Disabled';
    }
}

/* vim: set expandtab: */
