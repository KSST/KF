<?php
/**
 * Koch Framework Smarty View Helper.
 */

/**
 * This smarty function is part of "Koch Framework".
 *
 * Smarty {move_to} block plugin
 *
 * Filename: block_move_to.php<br>
 * Type:     block<br>
 * Name:     move_it<br>
 * Date:     Januar 11, 2009<br>
 * Purpose:  move all content in move_to blocks to the position in the html document which is defined by tag parameter
 *
 * Examples:<br>
 * <pre>
 * {move_to tag="pre_head_close"}
 *    <style type="text/css">
 *       h1{font-family:fantasy;}
 *    </style>
 * {/move_it}
 * </pre>
 *
 * @param array
 * @param string
 * @param Smarty
 * @param bool
 *
 * @return string
 */
function Smarty_block_move_to($params, $content, $smarty, &$repeat)
{
    if (empty($content)) {
        return;
    }

    if (isset($params['target'])) {
        $target = mb_strtoupper((string) $params['target']);
    } else {
        /*
         * the full errormessage is created by appending the first string
         * (one line would be over 130 chars long and the whitespaces matter)
         */
        $errormessage = 'You are using the <font color="#FF0033">{move_to}</font> command,';
        $errormessage .= ' but the <font color="#FF0033">Parameter "target" is missing.</font>';
        $errormessage .= ' Try to append one of the following parameters:';
        $errormessage .= ' <font color="#66CC00">';
        $errormessage .= 'target="pre_head_close" , target="post_body_open" , target="pre_body_close"</font>.';
        trigger_error($errormessage);
        unset($errormessage);

        return;
    }

    /*
     * define possible moveto positions
     * The x marks the position, the content will be moved to.
     */
    $valid_movement_positions = [
        'PRE_HEAD_CLOSE', //  x</head>
        'POST_BODY_OPEN', //  <body>x
        'PRE_BODY_CLOSE',
    ]; //  x</body>

    // whitelist: check if tag is a valid movement position
    if (!in_array($target, $valid_movement_positions, true)) {
        trigger_error(
            "Parameter 'target' needs one of the following values: pre_head_close, post_body_open, pre_body_close"
        );

        return;
    }

    /*
     * This inserts a comment, showing from which template a certain move is performed.
     * This makes it easier to determine the origin of the move operation.
     */

    $templatename = $smarty->getTemplateVars('templatename');

    $origin_start = '<!-- [Start] Segment moved from: ' . $templatename . " -->\n";
    $origin_end   = '<!-- [-End-] Segment moved from: ' . $templatename . " -->\n";

    $content = '@@@SMARTY:' . $target . ':BEGIN@@@';
    $content .= $origin_start . ' ' . trim($content) . "\n" . $origin_end;
    $content .= '@@@SMARTY:' . $target . ':END@@@';

    return $content;
}
