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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Filter for Smarty2 subtemplate moves.
 *
 * This is a Smarty2 related Filter.
 * Before the Smarty3 {block} tag was invented, there was no functionality
 * for assigning content from a child-template to the master-template.
 *
 * This filter works together with the {move_to} smarty viewhelper.
 * In the subtemplate the {move_to} command is used.
 * This inserts special text fragments into the template,
 * marking the positions of texts which are to be moved by this filter.
 * This filter detects these special text fragments in the output of smarty
 * and performs the moves accordingly.
 *
 * PRE_HEAD_CLOSE
 * POST_BODY_OPEN
 * PRE_BODY_CLOSE
 *
 * Purpose: detect block-tags, move content of such blocks, remove tags afterwards.
 */
class SmartyMoves implements FilterInterface
{
    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        /*
         * If the renderer is not smarty, then bypass the filter.
         */
        if ($request->getRoute()->getRenderEngine() !== 'smarty') {
            return;
        }

        /*
         * Get HttpResponse output buffer
         */
        $content = $response->getContent();

        /*
         * This matches the PRE_HEAD_CLOSE tag.
         * The X marks the position: X</head>
         */
        $matches = [];
        $regexp1 = '!@@@SMARTY:PRE_HEAD_CLOSE:BEGIN@@@(.*?)@@@SMARTY:PRE_HEAD_CLOSE:END@@@!is';
        preg_match_all($regexp1, $content, $matches);
        $content = preg_replace($regexp1, '', $content);
        $matches = array_keys(array_flip($matches[1]));
        foreach ($matches as $value) {
            $content = str_replace('</head>', $value . "\n" . '</head>', $content);
        }

        /*
         * This matches the POST_BODY_OPEN tag.
         * The X marks the position: <body>X
         */
        $matches = [];
        $regexp2 = '!@@@SMARTY:POST_BODY_OPEN:BEGIN@@@(.*?)@@@SMARTY:POST_BODY_OPEN:END@@@!is';
        preg_match_all($regexp2, $content, $matches);
        $content = preg_replace($regexp2, '', $content);
        $matches = array_keys(array_flip($matches[1]));
        foreach ($matches as $values) {
            $content = str_replace('<body>', '<body>' . "\n" . $value, $content);
        }

        /*
         * This matches the POST_BODY_OPEN tag.
         * The X marks the position: X</body>
         */
        $matches = [];
        $regexp3 = '!@@@SMARTY:PRE_BODY_CLOSE:BEGIN@@@(.*?)@@@SMARTY:PRE_BODY_CLOSE:END@@@!is';
        preg_match_all($regexp3, $content, $matches);
        $content = preg_replace($regexp3, '', $content);
        $matches = array_keys(array_flip($matches[1]));
        foreach ($matches as $values) {
            $content = str_replace('</body>', $value . "\n" . '</body>', $content);
        }

        /*
         * Replace the http response buffer
         */
        $response->setContent($content, true);
    }
}
