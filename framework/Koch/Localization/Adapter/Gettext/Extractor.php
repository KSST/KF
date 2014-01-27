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

namespace
        $class = 'Koch\Localization\Gettext\Extractors\\' . ucfirst($extractor);

        // was loaded before?
        if ($this->extractors[$extractor] !== null) {
            return $this->extractors[$extractor];
        } else {
            // /framework/Koch/Localization/Adapter/Gettext/Extractors/*NAME*.php
            $file = __DIR__ . '/Adapter/Gettext/Extractors/' . $extractor . '.php';

            if (true === is_file($file)) {
                include_once $file;
            } else {
                $this->throwException('Extractor file ' . $file . ' not found.');
            }

            if (false === class_exists($class)) {
                $this->throwException('File loaded, but Class ' . $extractor . ' not inside.');
            }
        }

        $this->extractors[$extractor] = new $class;

        $this->log('Extractor ' . $extractor . ' loaded.');

        return $this->extractors[$extractor];
    }

    /**
     * Assigns an extractor to an extension
     *
     * @param string $extension
     * @param string $extractor
     *
     * @return Koch_Gettext_Extractor
     */
    public function setExtractor($extension, $extractor)
    {
        // not already set
        if (false === isset($this->extractor[$extension]) and
            false === in_array($extractor, $this->extractor[$extension])) {
            $this->extractor[$extension][] = $extractor;
        } else { // already set

            return $this;
        }
    }

    /**
     * Removes all extractor settings
     *
     * @return Koch_Gettext_Extractor
     */
    public function removeAllExtractors()
    {
        $this->extractor = array();

        return $this;
    }

    /**
     * Saves extracted data into gettext file
     *
     * @param string $file
     * @param array  $data
     *
     * @return Koch_Gettext_Extractor
     */
    public function save($file, $data = null)
    {
        if ($data === null) {
            $data = $this->data;
        }

        // get dirname and check if dirs exist, else create it
        $dir = dirname($file);
        if (false === is_dir($dir) and false === @mkdir($dir, 0777, true)) {
            $this->throwException('ERROR: make directory failed!');
        }

        // check file permissions on output file
        if (true === is_file($file) and false === is_writable($file)) {
            $this->throwException('ERROR: Output file is not writable!');
        }

        // write data formatted to file
        file_put_contents($file, $this->formatData($data));

        $this->log('Output file ' . $file . ' created.');

        return $this;
    }

    /**
     * Returns a fileheader for a gettext portable object file.
     *
     * @param boolean $return_as_string True, returns a string (default) and false returns an array.
     *
     * @return mixed Array or String. Returns string by default.
     */
    public static function getPOFileHeader($return_as_string = true)
    {
        $output = array();
        $output[] = '# Gettext Portable Object Translation File.';
        $output[] = '#';
        $output[] = '# Koch Framework';
        $output[] = '# SPDX-FileCopyrightText: 2005-2024 Jens A. Koch';
        $output[] = '# SPDX-License-Identifier: MIT';
        $output[] = '#';
        $output[] = 'msgid ""';
        $output[] = 'msgstr ""';
        $output[] = '"Project-Id-Version: Koch Framework ' . APPLICATION_VERSION . '\n"';
        $output[] = '"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\n"';
        $output[] = '"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"';
        $output[] = '"Content-Type: text/plain; charset=UTF-8\n"';
        // @todo fetch plural form from locale description array
        $output[] = '"Plural-Forms: nplurals=2; plural=(n != 1);\n"';
        $output[] = '';

        if ($return_as_string === true) {
            return implode("\n", $output);
        } else { // return array

            return $output;
        }
    }

    /**
     * Formats fetched data to gettext portable object syntax
     *
     * @param array $data
     *
     * @return string
     */
    protected function formatData($data)
    {
        $pluralMatchRegexp = '#\%([0-9]+\$)*d#';

        $output = array();
        $output = self::getPOFileHeader(false);

        ksort($data);

        foreach ($data as $key => $files) {
            ksort($files);

            $slashed_key = self::addSlashes($key);

            foreach ($files as $file) {
                $output[] = '#: ' . $file; // = reference
            }

            $output[] = 'msgid "' . $slashed_key . '"';

            // check for plural
            if (0 < preg_match($pluralMatchRegexp, $key)) {
                $output[] = 'msgid_plural "' . $slashed_key . '"';
                $output[] = 'msgstr[0] "' . $slashed_key . '"';
                $output[] = 'msgstr[1] "' . $slashed_key . '"';
            } else { // no plural
                $output[] = 'msgstr "' . $slashed_key . '"';
            }

            $output[] = '';
        }

        return join("\n", $output);
    }

    /**
     * Escapes a string without breaking gettext syntax.
     *
     * @param string $string
     *
     * @return string
     */
    public static function addSlashes($string)
    {
        return addcslashes($string, self::ESCAPE_CHARS);
    }
}
