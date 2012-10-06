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

namespace Koch\Localization\Adapter\Gettext\Extractors;

/**
 * Gettext_Extractor_Template
 *
 * Extracts translation strings from templates by scanning for certain placeholders, like {t}, {_}.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Gettext
 */
class Template extends Base implements Extractor
{
    const L_DELIMITER = '{';
    const R_DELIMITER = '}';

    /**
     * This modified regexp is based on "tsmarty2c.php" by

     *
     * @const regexp to match the smarty curly bracket syntax
     */
    const REGEXP = "/__LD__\s*(__TAGS__)\s*([^__RD__]*)__RD__([^__LD__]*)__LD__\/\\1__RD__/";

    /**
     * The function tags to extract translation strings from
     *
     * @var array
     */
    protected $tags_to_scan = array('t', '_');

    /**
     * Parses given file and returns found gettext phrases
     *
     * @param string $file
     *
     * @return array
     */
    public function extract($file)
    {
        // load file
        $filecontent = file($file);

        // ensure we got the filecontent
        if (empty($filecontent)) {
            return;
        }

        // ensure we got defined some tags to scan for
        if (false === count($this->tags_to_scan)) {
            return;
        }

        // init vars
        $pathinfo = pathinfo($file);
        $data = array();

        /**
         *  construct the regular expression pattern
         */
        // join placeholders for multi-tag scan
        #$tags = $this->tags_to_scan[0];
        $tags = join('|', $this->tags_to_scan);

        // setup search/replace arrays
        $search  = array('__TAGS__', '__LD__', '__RD__');
        $replace = array($tags, self::L_DELIMITER, self::R_DELIMITER);

        // replace tags and delimiters in regexp pattern
        $pattern = str_replace($search, $replace, self::REGEXP);

        // parse file by lines
        foreach ($filecontent as $line => $line_content) {
            // grab the prefixed tags
            preg_match_all($pattern, $line_content, $matches);

            // no match
            if (empty($matches)) {
                continue;
            }

            // correct line number, because file[line1] = array[0]
            $calc_line = 1 + $line;

            foreach ($matches[3] as $match) {
                /**
                 *  $data array has the following structure
                 *  array('language-string') => array([0] => 'file:line')
                 */
                 $data[$match][] = $pathinfo['basename'] . ':' . $calc_line;

                unset($match);
            }
        }
        unset($filecontent);

        return $data;
    }
}
