<?php
/**
 * Smarty plugin.
 */

/**
 * Smarty Modifier to purify the html output via HTMLPurifier.
 * The library filters HTML tags by using a tag whitelist.
 *
 * @link http://htmlpurifier.org/
 *
 * @example
 * {$htmlcontent|purify}
 *
 * @param mixed Variable to firedebug
 *
 * @return string
 */
function smarty_modifier_purify($string)
{
    /**
     * @var object HTMLPurifier
     */
    static $purifier;

    if (isset($purifier) === false or class_exists('HTMLPurifier', false) === false) {
        include ROOT_LIBRARIES . 'IDS/vendors/htmlpurifier/HTMLPurifier.php';

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'ISO-8859-1');
        $config->set('HTML.Allowed', 'p,b,i,br,blockquote,em,li,ol,ul,strong,sub,sup,table,tbody,td,tfoot,th,thead,tr,u');
        $config->set('AutoFormat.AutoParagraph', true);

        $purifier = new HTMLPurifier($config);
    }

    return $purifier->purify($string);
}
