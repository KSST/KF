<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

/**
 * Koch Framework Smarty Viewhelper.
 *
 * Name:        tabpanel
 * Type:        function
 * Purpose:     This TAG inserts a jquery tabpanel
 */
function Smarty_block_tabpanel($params, $content, $smarty, &$repeat)
{
    $name = $params['name'] ?? '1';

    // replace whitespaces with underscore
    // else javascript selection will not work properly
    $name = str_replace(' ', '_', $name);

    // Initialize
    // @todo addJavascript('header', 'once', $js)
    // @todo addCss('once', $css);
    $start_tabpane = '<!-- Tabs with jQuery + YAML Accessible Tabs Plugin -->' . CR;
    $start_tabpane .= '<link rel="stylesheet" type="text/css" href="' . WWW_ROOT_THEMES_CORE . 'css/tabs.css" />' . CR;
    $start_tabpane .= '<script type="text/javascript" src="' . WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.tabs.js"></script>' . CR;

    $js = <<< EOF
<script type="text/javascript">
    $(document).ready(function () {
        $(".tabs").accessibleTabs({
            fx: "fadeIn",
            tabbody: '.tab-page',
            tabhead: '.tab',
            currentInfoText: '&raquo; ',
            currentInfoPosition: 'prepend',
            currentInfoClass: 'current-info'
          });
    });
</script>
EOF;

    $start_tabpane .= $js . CR;

    // Start TAB Pane
    $start_tabpane .= '<!-- START - TAB PANEL "' . $name . '" -->' . CR;
    $start_tabpane .= '<div class="tabs" id="tab-panel-' . $name . '"> ' . CR;

    // End TAB Pane
    $end_tabpane = '</div><!-- END - TAB PANEL "' . $name . '" -->' . CR;

    // Construct content for whole BLOCK
    /*
     * As of Smarty v3.1.6 the block tag is rendered at the opening AND closing tag
     * This results in a duplication of content.
     * To prevent this, we need to check that the content is oCRy rendered when the inner block (content)
     * is present.
     */
    if ($content !== null) {
        return $start_tabpane . $content . $end_tabpane;
    }
}
