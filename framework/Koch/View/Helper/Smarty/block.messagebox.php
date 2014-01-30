<?php
/**
 * Koch Framework Smarty Viewhelper
 *
 */

/**
 * This smarty function is part of "Koch Framework"
 *
 * Name:         messagebox
 * Type:         function
 * Purpose:     This TAG inserts a formatted messagebox (hint, notice, alert).
 *
 * @return string HTML of a messagebox.
 */
function Smarty_block_messagebox($params, $text, $smarty)
{
    $text = stripslashes($text);
    $textbox_type = null;
    $textbox_level = null;

    // set default type of messagebox to "div", if no type was given
    if (empty($params['type']) == true) {
        $textbox_type = 'div';
    } else {
        $textbox_type = $params['type'];
    }

    // whitelist for messagebox levels
    $messagebox_level = array( 'hint', 'notice', 'alert', 'info');

    if ($params['level'] !== null and in_array(mb_strtolower($params['level']), $messagebox_level)) {
        $textbox_level = mb_strtolower($params['level']);
    } else {
        return trigger_error('Please define a parameter level, e.g. hint, notice, alert, info.');
    }

    $tpl_vars = $smarty->getTemplateVars();

    $sprintfTextboxMessage  = '<link rel="stylesheet" type="text/css"' . 
        ' href="' . $tpl_vars['WWW_ROOT_THEMES_CORE'] . 'css/error.css" />';

    switch ($textbox_type) {
        default:
        case "div":
            $textbox_type = 'div';
            $sprintfTextboxMessage .= '<div class="messagebox %2$s">%3$s</div>';
            break;
        case "fieldset":
            $sprintfTextboxMessage .= '<fieldset class="error_help %s"><legend>%s</legend><em>%s</em></fieldset>';
            break;
    }

    return sprintf($sprintfTextboxMessage, $textbox_type, $textbox_level, $text);
}
