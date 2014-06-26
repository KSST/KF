<?php

/**
 * Convert $size to readable format.
 *
 * This determines prefixes for binary multiples according to IEC 60027-2, Second edition, 2000-11,
 * Letter symbols to be used in electrical technology - Part 2: Telecommunications and electronics.
 *
 * @param $bytes bytes
 * @return string
 */
function getSize($bytes)
{
    static $s = array('B', 'KB', 'MB', 'GB', 'TB'); //  'PB', 'EB', 'ZB', 'YB');
    $e = (int) (log($bytes) / (M_LN2 * 10));
    $size = $bytes / pow(1024, $e);

    return sprintf('%.2f' . $s[$e], $size);
}
