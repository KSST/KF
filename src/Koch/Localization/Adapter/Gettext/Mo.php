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
 * Class for Handling for Gettext "Machine Object" (.mo) files.
 */
class Mo
{
    /**
     * Writes a GNU gettext style machine object (mo-file).
     *
     * @link http://www.gnu.org/software/gettext/manual/gettext.html#MO-Files
     *
     * @param $hash
     * @param $file mo file to write.
     */
    public function write($hash, $file)
    {
        // sort by msgid
        ksort($hash, SORT_STRING);

        // our mo file data
        $mo = '';

        // header data
        $offsets = [];
        $ids     = '';
        $strings = '';

        foreach ($hash as $entry) {
            $id = $entry['msgid'];

            if ($entry['msgid_plural'] !== null) {
                $id .= "\x00" . $entry['msgid_plural'];
            }

            // context is merged into id, separated by EOT (\x04)
            if (($entry['msgctxt'] !== null) || array_key_exists('msgctxt', $entry)) {
                $id = $entry['msgctxt'] . "\x04" . $id;
            }

            // plural msgstrs are NUL-separated
            $str = implode("\x00", $entry['msgstr']);

            // keep track of offsets
            $offsets[] = [mb_strlen($ids), mb_strlen((string) $id), mb_strlen($strings), mb_strlen($str)];

            // plural msgids are not stored (?)
            $ids .= $id . "\x00";

            $strings .= $str . "\x00";
        }

        // keys start after the header (7 words) + index tables ($#hash * 4 words)
        // originally: 7 * 4 + count($hash) * 4 * 4
        $key_start = 28 + count($hash) * 16;

        // values start right after the keys
        $value_start = $key_start + mb_strlen($ids);

        // first all key offsets, then all value offsets
        $key_offsets   = [];
        $value_offsets = [];

        // calculate
        foreach ($offsets as $v) {
            [$o1, $l1, $o2, $l2] = $v;
            $key_offsets[]           = $l1;
            $key_offsets[]           = $o1 + $key_start;
            $value_offsets[]         = $l2;
            $value_offsets[]         = $o2 + $value_start;
        }

        $offsets = array_merge($key_offsets, $value_offsets);

        // write header
        $mo .= pack(
            'Iiiiiii',
            0x950412de,             // magic number
            0,                      // version
            count($hash),           // number of entries in the catalog
            28,                     // key index offset (7*4)
            28 + count($hash) * 8,  // value index offset (7*4 + length of hash*8)
            0,                      // hashtable size (unused, thus 0)
            $key_start              // hashtable offset
        );

        // offsets
        foreach ($offsets as $offset) {
            $mo .= pack('i', $offset);
        }

        // ids
        $mo .= $ids;

        // strings
        $mo .= $strings;

        file_put_contents($file, $mo);
    }
}
