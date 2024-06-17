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

namespace Koch\Localization\Adapter\Gettext;

/**
 * Class for handling of Gettext (.po) files.
 */
class Po
{
    /**
     * Reads a Gettext .po file.
     *
     * @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
     *
     * @param string $file Path to PO file.
     *
     * @return mixed|bool|array
     */
    public static function read($file)
    {
        // read .po file
        $fh = fopen($file, 'r');

        if ($fh === false) {
            // could not open file resource
            return false;
        }

        $result = [];
        $temp   = [];
        $state  = null;
        $fuzzy  = false;

        // iterate over lines
        while (($line = fgets($fh, 65536)) !== false) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$key, $data] = preg_split('/\s/', $line, 2);

            switch ($key) {
                case '#,': // flag...
                    $fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data), true);
                    // fall-through
                case '#':  // translator-comments
                case '#.': // extracted-comments
                case '#:': // reference...
                case '#|': // msgid previous-untranslated-string
                    // start a new entry
                    if (count($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)) {
                        if (false === $fuzzy) {
                            $result[] = $temp;
                        }

                        $temp  = [];
                        $state = null;
                        $fuzzy = false;
                    }
                    break;
                case 'msgctxt': // context
                case 'msgid': // untranslated-string
                case 'msgid_plural': // untranslated-string-plural
                    $state        = $key;
                    $temp[$state] = $data;
                    break;
                case 'msgstr': // translated-string
                    $state          = 'msgstr';
                    $temp[$state][] = $data;
                    break;
                default:
                    if (str_contains($key, 'msgstr[')) {
                        // translated-string-case-n
                        $state          = 'msgstr';
                        $temp[$state][] = $data;
                    } else {
                        // continued lines
                        switch ($state) {
                            case 'msgctxt':
                            case 'msgid':
                            case 'msgid_plural':
                                $temp[$state] .= "\n" . $line;
                                break;
                            case 'msgstr':
                                $temp[$state][count($temp[$state]) - 1] .= "\n" . $line;
                                break;
                            default:
                                // parse error
                                fclose($fh);

                                return false;
                        }
                    }
                    break;
            }
        }

        fclose($fh);

        // add final entry
        if ($state === 'msgstr') {
            $result[] = $temp;
        }

        // Cleanup data, merge multiline entries, reindex result for ksort
        $temp   = $result;
        $result = [];

        foreach ($temp as $entry) {
            foreach ($entry as &$v) {
                $v = self::poCleaner($v);

                if ($v === false) {
                    // parse error
                    return false;
                }
            }
            $result[$entry['msgid']] = $entry;
        }

        return $result;
    }

    /**
     * Cleans the po.
     *
     * @param string|array $x
     *
     * @return string Cleaned PO element.
     */
    private static function poCleaner($x)
    {
        if (true === is_array($x)) {
            foreach ($x as $k => $v) {
                // WATCH IT! RECURSION!
                $x[$k] = self::poCleaner($v);
            }
        } else {
            if ($x[0] === '"') {
                $x = mb_substr($x, 1, -1);
            }

            $x = str_replace("\"\n\"", '', $x);
            $x = str_replace('$', '\\$', $x);

            // #\Koch\Debug\Debug:firebug($x);

            // @todo which use case has this eval?
            //$x = @ eval ("return \"$x\";");
        }

        return $x;
    }
}
