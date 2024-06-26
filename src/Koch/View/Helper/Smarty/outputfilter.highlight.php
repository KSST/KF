<?php

/**
 * Smarty plugin.
 */
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.highlight.php
 * Type:     outputfilter
 * Name:     highlight
 * Version:  1.1
 * Date:     Sep 18, 2003
 * Version:  1.0
 * Date:     Aug 10, 2003
 * Purpose:  Adds Google-cache-like highlighting for terms in a
 *           template after its rendered. This can be used
 *           easily integrated with the wiki search functionality
 *           to provide highlighted search terms.
 * Install:  Drop into the plugin directory, call
 *           $smarty->load_filter('output','highlight');
 *           from application.
 * Author:   Greg Hinkle <ghinkl@users.sourceforge.net>
 *           patched by mose <mose@feu.org>
 *           Referer parsing by mdavey
 * -------------------------------------------------------------
 */
function smarty_outputfilter_highlight($source, $smarty)
{
    $highlight                 = $_REQUEST['highlight'];
    $feature_referer_highlight = $GLOBALS['feature_referer_highlight']; // @todo remove globals

    if (isset($feature_referer_highlight) && ${$feature_referer_highlight} === 'y') {
        $refererhi = _refererhi();
        if (($refererhi !== null) && !empty($refererhi)) {
            if (($highlight !== null) && !empty($highlight)) {
                $highlight = $highlight . ' ' . $refererhi;
            } else {
                $highlight = $refererhi;
            }
        }
    }

    if (!isset($highlight) || empty($highlight)) {
        return $source;
    }

    $source = preg_replace_callback(
        '~(?:<head>.*?</head>                          // head blocks
      |<div[^>]*nohighlight.*?</div>\{\*nohighlight  // div with nohightlight
      |<script[^>]+>.*?</script>                     // script blocks
      |onmouseover=(?:"[^"]*"|\'[^\']*\')            // onmouseover (user popup)
      |<[^>]*?>                                      // all html tags
      |(' . _enlightColor($highlight) . '))~xsi', '_enlightColor', $source);

    return $source;
}

function _enlightColor($matches)
{
    static $colword = [];
    if (is_string($matches)) { // just to set the color array
        // This array is used to choose colors for supplied highlight terms
        $colorArr = ['#ffff66', '#ff9999', '#A0FFFF', '#ff66ff', '#99ff99'];

        // Wrap all the highlight words with tags bolding them and changing
        // their background colors
        $i       = 0;
        $seaword = $seasep = '';
        $wordArr = preg_split('~%20|\+|\s+~', $matches);
        foreach ($wordArr as $word) {
            if ($word === '') {
                continue;
            }
            $seaword .= $seasep . preg_quote($word, '~');
            $seasep                        = '|';
            $colword[mb_strtolower($word)] = $colorArr[$i % 5];
            ++$i;
        }

        return $seaword;
    }
    // actual replacement callback
    if ($matches[1] !== null) {
        return '<span style="color:black; background-color:'
            . $colword[mb_strtolower((string) $matches[1])] . ';">' . $matches[1] . '</span>';
    }

    return $matches[0];
}

// helper function
// q= for Google, p= for Yahoo
function _refererhi()
{
    $referer = parse_url((string) $_SERVER['HTTP_REFERER']);
    parse_str($referer['query'], $vars);
    if ($vars['q'] !== null) {
        return $vars['q'];
    } else {
        if ($vars['p'] !== null) {
            return $vars['p'];
        }
    }
}
