<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Validation;

/**
 * Sanitizes the HTML body content.
 * Removes dangerous tags and attributes which might lead
 * to security issues like XSS or HTTP response splitting.
 */
class Sanitizer
{

    protected $allowedTags;
    protected $allowJavascriptEvents;
    protected $allowJavascriptInUrls;
    protected $allowObjects;
    protected $allowScript;
    protected $allowStyle;
    protected $allowInlineStyle;
    protected $additionalTags;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resetAll();
    }

    /**
     * (re)set all options to default value
     */
    public function resetAll()
    {
        $this->allowDOMEvents = false;
        $this->allowJavascriptInUrls = false;
        $this->allowStyle = false;
        $this->allowScript = false;
        $this->allowObjects = false;
        $this->allowInlineStyle = false;

        // HTML5 Tags
        // @link http://www.w3schools.com/html5/html5_reference.asp
        $this->allowedTags = '<a><abbr><address><area><article><aside>'        // <audio>
                . '<b><base><bdo><blockquote><body><br><button>
                   <canvas><caption><cite><code><col><colgroup><command>
                   <datalist><dd><details><del><dfn><div><dl><dt>'
                . '<em><eventsource>'                                           // <embed>
                . '<fieldset><figcaption><figure><footer><form>
                   <head><header><hgroup><hr><html><h1><h2><h3><h4><h5><h6>'
                . '<i><img><input><ins>'                                        // <iframe>
                . '<kbd><keygen>
                   <label><legend><li><link>
                   <mark><map><menu><meta><meter>
                   <nav><noscript>
                   <object><ol><optgroup><option><output>
                   <p><param><pre><progress>
                   <q>'
                . '<rp><rt>'                                                    // add <ruby> chinese characters
                . '<samp><script><section><select><small><source><span><strong><style><sub><summary><sup>
                   <table><tbody><td><textarea><tfoot><th><thead><time><title><tr>
                   <ul>
                   <var><video>
                   <wbr>';

        $this->additionalTags = '';
    }

    /**
     * Add additional tags to allowed tags
     *
     * @param string $tags
     */
    public function addAdditionalTags($tags)
    {
        $this->additionalTags .= $tags;
    }

    /**
     * Allow object, embed, applet and param tags in html
     */
    public function allowObjects()
    {
        $this->allowObjects = true;
    }

    /**
     * Allow DOM event on DOM elements
     */
    public function allowDOMEvents()
    {
        $this->allowDOMEvents = true;
    }

    /**
     * Allow script tags
     */
    public function allowScript()
    {
        $this->allowScript = true;
    }

    /**
     * Allow the use of javascript: in urls
     */
    public function allowJavascriptInUrls()
    {
        $this->allowJavascriptInUrls = true;
    }

    /**
     * Allow style tags and attributes
     */
    public function allowStyle()
    {
        $this->allowStyle = true;
    }

    /**
     * Helper to allow all javascript related tags and attributes
     */
    public function allowAllJavascript()
    {
        $this->allowDOMEvents();
        $this->allowScript();
        $this->allowJavascriptInUrls();
    }

    /**
     * Allow all tags and attributes
     */
    public function allowAll()
    {
        $this->allowAllJavascript();
        $this->allowObjects();
        $this->allowStyle();
    }

    /**
     * Filter URLs to avoid HTTP response splitting attacks
     *
     * @param  string $url
     * @return string filtered url
     */
    protected function filterHTTPResponseSplitting($url)
    {
        $dangerousCharactersPattern = '~(\r\n|\r|\n|%0a|%0d|%0D|%0A)~';

        return preg_replace($dangerousCharactersPattern, '', $url);
    }

    /**
     * Remove potential javascript in urls
     *
     * @param  string $str
     * @return string filtered url
     */
    protected function removeJavascriptURL($str)
    {
        $HTML_Sanitizer_stripJavascriptURL = 'javascript:[^"]+';

        $str = preg_replace("/$HTML_Sanitizer_stripJavascriptURL/i", '', $str);

        return $str;
    }

    /**
     * Remove potential flaws in urls
     *
     * @param  string $url
     * @return string filtered url
     */
    protected function sanitizeURL($url)
    {
        if (!$this->allowJavascriptInUrls) {
            $url = $this->removeJavascriptURL($url);
        }

        $url = $this->filterHTTPResponseSplitting($url);

        return $url;
    }

    /**
     * Callback for PCRE
     *
     * @param  array  $matches
     * @return string
     * @see     sanitizeURL
     */
    protected function sanitizeURLCallback($matches)
    {
        return 'href="' . $this->sanitizeURL($matches[1]) . '"';
    }

    /**
     * Remove potential flaws in href attributes
     *
     * @param  string $str html tag
     * @return string filtered html tag
     */
    protected function sanitizeHref($str)
    {
        $HTML_Sanitizer_URL = 'href="([^"]+)"';

        return preg_replace_callback("/$HTML_Sanitizer_URL/i", array(&$this, '_sanitizeURLCallback'), $str);
    }

    /**
     * Callback for PCRE
     *
     * @param  array  $matches
     * @return string
     * @see     sanitizeURL
     */
    protected function sanitizeSrcCallback($matches)
    {
        return 'src="' . $this->sanitizeURL($matches[1]) . '"';
    }

    /**
     * Remove potential flaws in href attributes
     *
     * @param  string $str html tag
     * @return string filtered html tag
     */
    protected function sanitizeSrc($str)
    {
        $HTML_Sanitizer_URL = 'src="([^"]+)"';

        return preg_replace_callback("/$HTML_Sanitizer_URL/i", array(&$this, '_sanitizeSrcCallback'), $str);
    }

    /**
     * Remove dangerous attributes from html tags
     *
     * @param  string $str html tag
     * @return string filtered html tag
     */
    protected function removeEvilAttributes($str)
    {
        if (!$this->allowDOMEvents) {
            $str = preg_replace_callback('/<(.*?)>/i', array(&$this, '_removeDOMEventsCallback'), $str);
        }

        if (!$this->allowStyle) {
            $str = preg_replace_callback('/<(.*?)>/i' , array(&$this, '_removeStyleCallback'), $str);
        }

        return $str;
    }

    /**
     * Remove DOM events attributes from html tags
     *
     * @param  string $str html tag
     * @return string filtered html tag
     */
    protected function removeDOMEvents($str)
    {
        $str = preg_replace('/\s*=\s*/', '=', $str);

        $HTML_Sanitizer_stripAttrib = '(onclick|ondblclick|onmousedown|'
                . 'onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|'
                . 'onkeyup|onfocus|onblur|onabort|onerror|onload)'
        ;

        $str = stripslashes(preg_replace("/$HTML_Sanitizer_stripAttrib/i", 'forbidden', $str));

        return $str;
    }

    /**
     * Callback for PCRE
     *
     * @param  array  $matches
     * @return string
     * @see     removeDOMEvents
     */
    protected function removeDOMEventsCallback($matches)
    {
        return '<' . $this->removeDOMEvents($matches[1]) . '>';
    }

    /**
     * Remove style attributes from html tags
     *
     * @param  string $str html tag
     * @return string filtered html tag
     */
    protected function removeStyle($str)
    {
        $str = preg_replace('/\s*=\s*/', '=', $str);

        $HTML_Sanitizer_stripAttrib = '(style)';

        $str = stripslashes(preg_replace("/$HTML_Sanitizer_stripAttrib/i", 'forbidden', $str));

        return $str;
    }

    /**
     * Callback for PCRE
     *
     * @param  array  $matches
     * @return string
     * @see     removeStyle
     */
    protected function removeStyleCallback($matches)
    {
        return '<' . $this->removeStyle($matches[1]) . '>';
    }

    /**
     * Remove dangerous HTML tags
     *
     * @param string $str html code
     *
     * @return string filtered url
     */
    protected function removeEvilTags($str)
    {
        $allowedTags = $this->allowedTags;

        if ($this->allowScript) {
            $allowedTags .= '<script>';
        }

        if ($this->allowStyle) {
            $allowedTags .= '<style>';
        }

        if ($this->allowObjects) {
            $allowedTags .= '<object><embed><applet><param>';
        }

        $allowedTags .= $this->additionalTags;

        // $str = strip_tags($str, $allowedTags );

        $str = $this->stripTags($str, $allowedTags);

        return $str;
    }

    /**
     * Remove unwanted tags
     *
     * @param string $str     html
     * @param string $tagList allowed tag list
     */
    protected function stripTags($str, $tagList)
    {
        // 1. prepare allowed tags list
        $search = array('<', '>', '><');
        $replace = array('', '', '|');
        $tagList = str_replace($search, $replace, $tagList);

        // 2. replace </tag> by [[/tag]] in close tags for allowed tags
        $closeTags = '~' . '\</(' . $tagList . ')([^\>\<]*)\>' . '~'; // close tag

        $str = preg_replace($closeTags, "[[/\\1]]", $str);

        // ?! = do not match
        $autoAndOpenTags = '~('
                . '\<(?!' . $tagList . ')[^\>\<]*(/){0,1}\>' // auto
                . ')~';

        // 3. replace not allowed tags by ''
        $str = preg_replace($autoAndOpenTags, '', $str);

        // 4. replace [[/tag]] by </tag> for allowed tags
        $closeTags = '~' . '\[\[/(' . $tagList . ')([^\]]*)\]\]' . '~'; // close tag

        $str = preg_replace($closeTags, "</\\1>", $str);

        return $str;
    }

    /**
     * Sanitize HTML
     *
     * - removes  dangerous tags and attributes
     * - cleand urls
     *
     * @param  string $html html code
     * @return string sanitized html code
     */
    public function sanitize($html)
    {
        $html = $this->removeEvilTags($html);

        $html = $this->removeEvilAttributes($html);

        $html = $this->sanitizeHref($html);

        $html = $this->sanitizeSrc($html);

        return $html;
    }
}
