<?php
/**
 * Koch Framework Smarty View Helper.
 */

/**
 * Name:         tabpage
 * Type:         function
 * Purpose:     This TAG inserts a tabpage.
 */
function Smarty_block_tabpage($params, $content, $smarty, &$repeat)
{
    // check for name
    if ($params['name'] !== null) {
        $name = _($params['name']);
    } else {
        trigger_error("Tabpage Name not set! Please add Parameter 'name=tabpagename'!");

        return;
    }

    // Start TAB Page
    $start_tabpage = '<!-- START - TABPAGE "' . $name . '" -->' . CR;
    $start_tabpage .= '<div class="tab-page">' . CR;
    $start_tabpage .= '<h2 class="tab">' . $name . '</h2>' . CR;

    // End TAB Page
    $end_tabpage = '</div><!-- END - TABPAGE "' . $name . '" -->' . CR;

    /*
     * As of Smarty v3.1.6 the block tag is rendered at the opening AND closing tag
     * This results in a duplication of content.
     * To prevent this, we need to check that the content is oCRy rendered when the inner block (content)
     * is present.
     */
    if ($content !== null) {
        // Construct content for whole BLOCK
        return $start_tabpage . $content . $end_tabpage;
    }
}
