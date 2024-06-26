<?php
/**
 * Koch Framework Smarty View Helper.
 */

/**
 * Smarty Function to output the addtoany social bookmark/sharing button.
 *
 * @link http://www.addtoany.com
 *
 * @example
 * {addtonany}
 *
 * @return string
 */
function Smarty_function_addtoany()
{
    $str = <<<EOD
<!--
     AddToAny - Social Bookmarks  http://www.addtoany.com/buttons/customize/
-->
<div id="socialbookmarks">
    <a class="a2a_dd" href="http://www.addtoany.com/share_save">
        <img src="http://static.addtoany.com/buttons/share_save_171_16.png" width="171" height="16" alt="Share/Save/Bookmark" />
    </a>
    <script type="text/javascript">
    //<![CDATA[
    a2a_config = {
        linkname: document.title,
        linkurl: document.URL,
        num_services: 10,
        show_title: 1
    };
    //]]>
    </script>
    <script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>
</div>
EOD;

    return $str;
}
