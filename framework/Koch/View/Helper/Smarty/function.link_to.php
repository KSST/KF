<?php
/**
 * Smarty plugin.
 */

/**
 * URL Generator for Smarty Templates.
 *
 * Examples:
 * {link_to href="/news/show"}
 *
 * Type:     function<br>
 * Name:     a<br>
 * Purpose:  Generates the proper URL from the href parameter given<br>
 *
 * @param array $params
 *
 * @return string
 */
function Smarty_function_link_to($params)
{
    // method parameter "href"
    if (empty($params['href'])) {
        $errormessage = 'You are using the <font color="#FF0033">{link_to}</font> command, but the <font color="#FF0033">Parameter "href" is missing.</font>';
        $errormessage .= ' Try to append the parameter in the following way: <font color="#66CC00">href="/news/show"</font>.';
        trigger_error($errormessage);

        return;
    } else {
        // convert from internal slashed format to URL
        return \Koch\Router\Router::buildURL($params['href']);
    }
}
