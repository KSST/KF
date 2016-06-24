<?php
/**
 * Smarty Help Tag
 * Displays help text of this module.
 *
 * Examples:
 * <pre>
 * {help}
 * </pre>
 *
 * Type:     function<br>
 * Name:     help<br>
 * Purpose:  displays help.tpl for a module, if existing<br>
 *
 * @param array  $params
 * @param Smarty $smarty
 *
 * @return string
 */
function Smarty_function_help($params, $smarty)
{
    $modulename = $smarty->getTemplateVars('template_of_module');

    $tpl = $modulename . '/view/smarty/help.tpl';

    // load the help template from the module path ->  app/modules/modulename/view/help.tpl
    if ($smarty->templateExists($tpl)) {
        return $smarty->fetch($tpl);
    }

    return 'Help Template not found.';
}
