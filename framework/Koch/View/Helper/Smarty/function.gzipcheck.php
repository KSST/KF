<?php
/**
 * Smarty plugin
 */

/**
 * smarty_function_gzipcheck
 */
function Smarty_function_gzipcheck($params, $smarty)
{
    if (ini_get('zlib.output_compression') == 1) {
        echo 'Enabled (zlib)';
    } else {
        echo 'Disabled';
    }
}
