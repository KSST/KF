<?php
/**
 * Koch Framework - Smarty Viewhelper for rendering \Koch\Session\FlashMessages.
 */
function Smarty_function_flashmessages($params, $smarty)
{
    // render only a certain type of flashmessages
    if ($params['type'] !== null) {
        return \Koch\Session\FlashMessages::render($params['type']);
    }

    // render all
    return \Koch\Session\FlashMessages::render();
}
