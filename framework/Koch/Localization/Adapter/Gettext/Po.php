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

namespace Koch\Localization\Adapter\Gettext;

/**
 * Gettext_POFile
 *
 * Handling for Gettext (.po) Files.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Gettext
 */
class Po
{
    /**
     * Reads a Gettext .po file
     *
     * @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
     *
     * @param  string              $file Path to PO file.
     * @return mixed|boolean|array
     */
    public static function read($file)
    {
        // read .po file
        $fh = fopen($file, 'r');

        if ($fh === false) {
            // could not open file resource
            return false;
        }

        // results array
        $hash = array();

        // temporary array
        $temp = array();

        // state
        $state = null;
        $fuzzy = false;

        // iterate over lines
        while (($line = fgets($fh, 65536)) !== false) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            list ($key, $data) = preg_split('/\s/', $line, 2);

            switch ($key) {
                case '#,': // flag...

                    $fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data));

                case '#':  // translator-comments

                case '#.': // extracted-comments

                case '#:': // reference...

                case '#|': // msgid previous-untranslated-string
                    // start a new entry
                    if (count($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)) {
                        if (false === $fuzzy) {
                            $hash[] = $temp;
                        }

                        $temp = array();
                        $state = null;
                        $fuzzy = false;
                    }
                    break;

                case 'msgctxt': // context

                case 'msgid': // untranslated-string

                case 'msgid_plural': // untranslated-string-plural

                    $state = $key;
                    $temp[$state] = $data;
                    break;

                case 'msgstr': // translated-string

                    $state = 'msgstr';
                    $temp[$state][] = $data;
                    break;

                default :

                    if (strpos($key, 'msgstr[') !== false) {
                        // translated-string-case-n
                        $state = 'msgstr';
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

                            default :
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
            $hash[] = $temp;
        }

        // Cleanup data, merge multiline entries, reindex hash for ksort
        $temp = $hash;
        $hash = array();

        foreach ($temp as $entry) {
            foreach ($entry as & $v) {
                $v = self::po_clean_helper($v);

                if ($v === false) {
                    // parse error
                    return false;
                }
            }
            $hash[$entry['msgid']]= $entry;
        }

        return $hash;
    }

    private static function po_clean_helper($x)
    {
        if (true === is_array($x)) {
            foreach ($x as $k => $v) {
                // WATCH IT! RECURSION!
                $x[$k]= self::po_clean_helper($v);
            }
        } else {
            if ($x[0] === '"') {
                $x = mb_substr($x, 1, -1);
            }

            $x = str_replace("\"\n\"", '', $x);
            $x = str_replace('$', '\\$', $x);

            // @todo which use case has this eval?
            // #\Koch\Debug\Debug:firebug($x);
            //$x = @ eval ("return \"$x\";");
        }

        return $x;
    }
}
