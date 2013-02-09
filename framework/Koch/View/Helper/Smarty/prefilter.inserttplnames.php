<?php
/**
 * Smarty plugin
 */

/**
 * Koch FrameworkSmarty Plugin
 * --------------------------------------------------------
 * File:    prefilter.inserttplnames.php
 * Type:    prefilter
 * Name:    inserttplcomment
 * Version: 1.0
 * Date:    03 Jun 2006
 * Purpose: Add Comment with Teplatename at begin & end of included tpl
 * Install: Place in your (local) plugins directory and add the call:
 *          $smarty->load_filter('pre', 'inserttplnames');
 * Author:  Jens-André Koch
 * --------------------------------------------------------
 */
function smarty_prefilter_inserttplnames($tpl_source, $compiler)
{
    $html = "\n<!-- [-Start-] Included Template {\$smarty.current_dir}/{\$smarty.template} -->\n";
    $html .= $tpl_source;
    $html .= "\n<!-- [-End-] Included Template {\$smarty.current_dir}/{\$smarty.template}  -->\n";

    return $html;
}
