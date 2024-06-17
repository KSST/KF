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
 * Class for rendering HTML output.
 *
 * The class provides helper methods to output html-tag elements.
 */
class Html /* extends DOMDocument */
{
    /**
     * Renders title tag.
     *
     * @param string $title
     *
     * @return string
     */
    public static function title($title)
    {
        return '<title>' . $title . '</title>' . CR;
    }

    /**
     * Renders meta.
     *
     * @param string $name  the meta name
     * @param string $value the meta value
     *
     * @return string
     */
    public static function meta($name, $value)
    {
        return '<meta name="' . $name . '" content="' . $value . '">' . CR;
    }

    /**
     * Renders the HTML Tag <a href=""></a>.
     *
     * @param string $url        The URL (href).
     * @param string $text       The text linking to the URL.
     * @param array  $attributes Additional HTML Attribute as string.
     *
     * @return string html
     */
    public static function a($url, $text, $attributes = [])
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<a href="' . $url . '" ' . $html_attributes . '>' . $text . '</a>';
    }

    /**
     * Render tag a-tag with mailto-target <a href="mailto:">text</a>.
     *
     * @param string $mail  the email address
     * @param string $title the email title.
     *
     * @return string
     */
    public static function mailto($mail = '', $title = '')
    {
        if (empty($title)) {
            $title = $mail;
        }

        return '<a href="mailto:' . $mail . '">' . $title . '</a>';
    }

    /**
     * Renders the HTML Tag <span></span>.
     *
     * @param string $text
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function span($text, $attributes = [])
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<span' . $html_attributes . '>' . $text . '</div>';
    }

    /**
     * Renders the HTML Tag <div></div>.
     *
     * @param string $text       string
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function div($text, $attributes = [])
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<div' . $html_attributes . '>' . $text . '</div>';
    }

    /**
     * Renders the HTML Tag <p></p>.
     *
     * @param string $text       string
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function p($text, $attributes = [])
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<p' . $html_attributes . '>' . $text . '</p>';
    }

    /**
     * Renders the HTML Tag <img></img>.
     *
     * @param string $link
     * @param array  $attributes
     *
     * @return string html
     */
    public static function img($link, $attributes = [])
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<img' . $html_attributes . ' src="' . $link . '" />';
    }

    /**
     * Renders icon tag.
     *
     * @param string $url the url of the icon.
     *
     * @return string
     */
    public static function icon($url)
    {
        return sprintf('<link rel="icon" href="%s" type="image/x-icon" />', $url);
    }

    /**
     * HTML Tag Rendering
     * Builds a list from an multidimensional attributes array.
     *
     * @example
     * $attributes = array('UL-Heading-A',
     *                  array('LI-Element-A','LI-Element-B'),
     *                     'UL-Heading-1',
     *                  array('LI-Element-1','LI-Element-2')
     *                    );
     * self::liste($attributes);
     *
     * @param array $attributes array of attributes
     *
     * @return string html
     */
    public static function liste($attributes)
    {
        $html = '';

        $html .= '<ul>';
        foreach ($attributes as $attribute) {
            if (is_array($attribute)) {
                // watch out! recursion
                $html .= self::liste($attribute);
            } else {
                $html .= '<li>' . $attribute . '</li>' . CR;
            }
        }
        $html .= '</ul>' . CR;

        return $html;
    }

    /**
     * HTML Tag <h1>.
     *
     * @param string $text string
     *
     * @return string html
     */
    public static function h1($text)
    {
        return '<h1>' . $text . '</h1>';
    }

    /**
     * HTML Tag <h2>.
     *
     * @param $text string
     *
     * @return string html
     */
    public static function h2($text)
    {
        return '<h2>' . $text . '</h2>';
    }

    /**
     * HTML Tag <h3>.
     *
     * @param string $text string
     *
     * @return string html
     */
    public static function h3($text)
    {
        return '<h3>' . $text . '</h3>';
    }

    /**
     * Render the attributes for usage in an tag element.
     *
     * @param array $attributes array of attributes
     *
     * @return string Renders the HTML String of Attributes
     */
    public static function renderAttributes(array $attributes = [])
    {
        $html = '';

        if (is_array($attributes)) {
            // insert all attributes, but ignore null values
            foreach ($attributes as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                $html .= ' ' . $key . '"' . $value . '"';
            }
        }

        return $html;
    }

    /**
     * Render an HTML Element.
     *
     * @example
     * echo self::renderElement('tagname', array('attribute_name'=>'attribut_value'), 'text');
     *
     * @param string $tagname    Name of the tag to render
     * @param string $text       string
     * @param array  $attributes array of attributes
     *
     * @return string html with Attributes
     */
    public static function renderElement($tagname, $text = null, $attributes = [])
    {
        if (method_exists(self, $tagname)) {
            if ($attributes['src'] !== null) {
                return self::$tagname($attributes['src'], $text, $attributes);
            } elseif ($attributes['href'] !== null) {
                return self::$tagname($attributes['href'], $text, $attributes);
            } else {
                return self::$tagname($text, $attributes);
            }
        }

        $html = '<' . $tagname . self::renderAttributes($attributes);

        // close tag with slash, if not appending text
        if ($text === null) {
            $html .= '/>';
        } else { // close opening tag
            $html .= '>' . $text . '</' . $tagname . '>' . CR;
        }

        return $html;
    }
}
