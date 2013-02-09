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

use Koch\Localization\ExtractorBase;
use Koch\Localization\ExtractorInterface;

/**
 * Koch Framework - Class for extracting Gettext string from PHP
 *
 * Extracts translation strings by scanning for certain functions: translate(), t(), _().
 */
class Php extends ExtractorBase implements ExtractorInterface
{
    /**
     * The function tags to extract translation strings from
     *
     * @var array
     */
    protected $tags_to_scan = array('translate', 't', '_');

    /**
     * Parses given file and returns found gettext phrases
     *
     * @param string $file
     *
     * @return array
     */
    public function extract($file)
    {
        $pInfo = pathinfo($file);
        $data = array();
        $tokens = token_get_all(file_get_contents($file));
        $next = false;

        foreach ($tokens as $c) {
            if (true === is_array($c)) {
                if ($c[0] !== T_STRING and $c[0] !== T_CONSTANT_ENCAPSED_STRING) {
                    continue;
                }

                if ($c[0] === T_STRING and true === in_array($c[1], $this->tags_to_scan)) {
                    $next = true;
                    continue;
                }

                if ($c[0] === T_CONSTANT_ENCAPSED_STRING and $next === true) {
                    $data[substr($c[1], 1, -1)][] = $pInfo['basename'] . ':' . $c[2];
                    $next = false;
                }
            } else {
                if ($c === ')') {
                    $next = false;
                }
            }
        }

        return $data;
    }
}
