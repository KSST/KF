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

namespace Koch\View\Helper;

/**
 * Class for BBCode Handling (Wrapper) and Syntax Highlighting.
 *
 * It's a wrapper class for
 * a) GeShi Code/Syntax Highligther
 *    This is used with the code tags, like [code]<?php ... ?>[/code]
 * b) bbcode_stringparser.
 */
class Bbcode
{
    /**
     * @var object instance of StringParser_BBCode
     */
    public $bbcode;

    public function __construct()
    {
        // Include Stringpaser_bbcode Class
        if (false === class_exists('StringParser_BBCode', false)) {
            include VENDOR_PATH . 'bbcode/stringparser_bbcode.class.php';
        }

        // Instantiate the object
        $this->bbcode = new StringParser_BBCode();

        $this->setupDefaultBBCodes();

        $this->initializeBBCodesFromDatabase();
    }

    /**
     * Setup some default BBCodes
     * - Conversions and Filters
     * - Standard Elements like
     *   - url, link, img, code.
     */
    public function setupDefaultBBCodes()
    {
        /*
         * Conversions & Filters
         */
        $this->bbcode->addFilter(STRINGPARSER_FILTER_PRE, [$this, 'convertlinebreaks']);
        $this->bbcode->addParser(['block', 'inline', 'link', 'listitem'], 'htmlspecialchars');
        $this->bbcode->addParser(['block', 'inline', 'link', 'listitem'], 'nl2br');

        /*
         * Generate Standard BB Codes
         */
        /*
         * BB Code: [url][/url]
         */
        $this->bbcode->addCode(
            'url',
            'usecontent?',
            [$this, 'do_bbcode_url'],
            ['usecontent_param' => 'default'],
            'link',
            ['listitem', 'block', 'inline'],
            ['link']
        );

        /*
         * BB Code: [link][/link]
         */
        $this->bbcode->addCode(
            'link',
            'callback_replace_single',
            [$this, 'do_bbcode_url'],
            [],
            'link',
            ['listitem', 'block', 'inline'],
            ['link']
        );

        /*
         * BB Code: [link][/link]
         */
        $this->bbcode->addCode(
            'img',
            'usecontent',
            [$this, 'do_bbcode_img'],
            [],
            'image',
            ['listitem', 'block', 'inline', 'link'],
            []
        );

        /*
         * BB Code: [code][/code]
         * This uses geshi syntax highlighting.
         */
        $this->bbcode->addCode(
            'code',
            'usecontent?',
            [$this, 'do_bbcode_code'],
            ['usecontent_param' => 'default'],
            'code',
            ['listitem', 'block', 'inline'],
            ['code']
        );

        $this->bbcode->setOccurrenceType('img', 'image');
    }

    /**
     * loads all bbcodes stored in database and assigns them to the bbcode parser object.
     */
    public function initializeBBCodesFromDatabase()
    {
        // Load all BB Code Definition from Database
        $bbcodes = Doctrine_Query::create()->select('*')->from('CsBbCode')->execute();

        /*
         * Add the BBCodes from DB via addCode
         */
        foreach ($bbcodes as $code) {
            // allowed
            $allowed_in = explode(',', (string) $code['allowed_in']);

            // not allowed
            $not_allowed_in = explode(',', (string) $code['not_allowed_in']);

            /*
             * assign the code via stringparser object and its method addCode()
             */
            $this->bbcode->addCode(
                $code['name'],
                'simple_replace',
                null,
                ['start_tag' => $code['start_tag'], 'end_tag' => $code['end_tag']],
                $code['content_type'],
                $allowed_in,
                $not_allowed_in
            );
        }
    }

    /**
     * Parse the text and apply BBCode.
     *
     * @param $text the string to parse and to apply the bbcode formatting to
     *
     * @return bbcode parsed text
     */
    public function parse($text)
    {
        return $this->bbcode->parse($text);
    }

    /**
     * Handle BB Code URLs.
     *
     * @param string
     * @param array
     * @param string
     * @param mixed
     * @param mixed
     *
     * @return bool|string url
     *
     * @todo $params and $node_objects are unuseed check
     */
    private function doBBCodeUrl($action, $attributes, $content, $params, $node_object)
    {
        if ($action === 'validate') {
            return true;
        }

        if (!isset($attributes['default'])) {
            return '<a href="' . htmlspecialchars((string) $content) . '">' . htmlspecialchars((string) $content) . '</a>';
        }

        return '<a href="' . htmlspecialchars((string) $attributes['default']) . '">' . $content . '</a>';
    }

    /**
     * Handle Pictures.
     *
     * @todo comment params
     *
     * @return bool|string string
     */
    private function doBBCodeImg($action, $attributes, $content, $params, $node_object)
    {
        if ($action === 'validate') {
            return true;
        }

        return '<img src="' . htmlspecialchars((string) $content) . '" alt="">';
    }

    /**
     * Handle PHP Code Hightlightning with GeShi.
     *
     * @return codehighlighted string
     */
    private function doBBCodeCode($action, $attributes, $content, $params, $node_object)
    {
        if ($action === 'validate') {
            return true;
        }

        // Include & Instantiate GeSHi
        if (false === class_exists('GeSHi', false)) {
            include VENDOR_PATH . '/geshi/geshi.php';
        }

        $geshi = new GeSHi($content, $attributes['default']);

        return $geshi->parse_code();
    }
}
