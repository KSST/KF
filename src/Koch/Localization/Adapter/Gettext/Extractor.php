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
 * Gettext Extractor.
 *
 * Gettext extraction is normally performed by the "xgettext" tool.
 * http://www.gnu.org/software/hello/manual/gettext/xgettext-Invocation.html
 *
 * This is a php implementation of a gettext extractor on basis of preg_matching.
 * The extractor matches certain translation functions, like translate('term') or t('term') or _('term').
 * and their counterparts in templates, often {t('term')} or {_('term')}.
 */
class Extractor
{
    /**
     * @var resource
     */
    public $logHandler;

    /**
     * @var array
     */
    public $inputFiles = [];

    /**
     * @var array
     */
    public $extractors = [
        'php' => ['PHP'],
        'tpl' => ['PHP', 'Template'],
    ];

    /**
     * @var array
     */
    public $data = [];

    /**
     *  @var array
     */
    protected $extractorStore = [];

    /**
     * Log setup.
     *
     * @param string|bool $logToFile Bool or path of custom log file
     */
    public function __construct($logToFile = false)
    {
        // default log file
        if (false === $logToFile) {
            $this->logHandler = fopen(ROOT_LOGS . 'gettext-extractor.log', 'w');
        } else { // custom log file
            $this->logHandler = fopen($logToFile, 'w');
        }
    }

    /**
     * Close the log handler if needed.
     */
    public function __destruct()
    {
        if (is_resource($this->logHandler)) {
            fclose($this->logHandler);
        }
    }

    /**
     * Writes messages into log or dumps them on screen.
     *
     * @param string $message
     *
     * @return string Html Log Message if logHandler resource is false.
     */
    public function log($message)
    {
        if (is_resource($this->logHandler)) {
            fwrite($this->logHandler, $message . "\n");
        } else {
            echo $message . "\n <br/>";
        }
    }

    /**
     * Exception factory.
     *
     * @param string $message
     *
     * @throws \Koch\Exception\Exception
     */
    protected function throwException($message)
    {
        if (empty($message)) {
            $message = 'Something unexpected occured. See Koch_Gettext_Extractor log for details.';
        }

        $this->log($message);

        throw new \Koch\Exception\Exception($message);
    }

    /**
     * Scans given files or directories (recursively) for input files.
     *
     * @param string $resource File or directory
     */
    protected function scan($resource)
    {
        if (false === is_dir($resource) and false === is_file($resource)) {
            $this->throwException('Resource ' . $resource . ' is not a directory or file.');
        }

        if (true === is_file($resource)) {
            $this->inputFiles[] = realpath($resource);

            return;
        }

        // It's a directory
        $resource = realpath($resource);

        if (false === $resource) {
            return;
        }

        $iterator = dir($resource);

        if (false === $iterator) {
            return;
        }

        while (false !== ($entry = $iterator->read())) {
            if ($entry === '.' or $entry === '..' or  $entry === '.svn') {
                continue;
            }

            $path = $resource . DIRECTORY_SEPARATOR . $entry;

            if (false === is_readable($path)) {
                continue;
            }

            if (true === is_dir($path)) {
                $this->scan($path);
                continue;
            }

            if (true === is_file($path)) {
                $info = pathinfo($path);

                if (false === isset($this->extractors[$info['extension']])) {
                    continue;
                }

                $this->inputFiles[] = realpath($path);
            }
        }

        $iterator->close();
    }

    /**
     * Extracts gettext keys from multiple input files using multiple extraction adapters.
     *
     * @param array $inputFiles Array, defining a set of files.
     *
     * @return array All gettext keys of all input files.
     */
    protected function extract($inputFiles)
    {
        foreach ($inputFiles as $inputFile) {
            if (false === file_exists($inputFile)) {
                $this->throwException('Invalid input file specified: ' . $inputFile);
            }

            if (false === is_readable($inputFile)) {
                $this->throwException('Input file is not readable: ' . $inputFile);
            }

            $this->log('Extracting data from file ' . $inputFile);

            // Check file extension
            $fileExtension = pathinfo($inputFile, PATHINFO_EXTENSION);

            foreach ($this->extractors as $extension => $extractor) {
                // check, if the extractor handles a file extension like this
                if ($fileExtension !== $extension) {
                    continue;
                }

                $this->log('Processing file ' . $inputFile);

                foreach ($extractor as $extractorName) {
                    $extractor     = $this->getExtractor($extractorName);
                    $extractorData = $extractor->extract($inputFile);

                    $this->log(' Extractor ' . $extractorName . ' applied.');

                    // do not merge if incomming array is empty
                    if (false === empty($extractorData)) {
                        $this->data = array_merge_recursive($this->data, $extractorData);
                    }
                }
            }
        }

        $this->log('Data exported successfully');

        return $this->data;
    }

    /**
     * Factory Method - Gets an instance of a Koch_Gettext_Extractor.
     *
     * @param string $extractor
     *
     * @return object Extractor Object implementing Koch\Localization\Gettext\Extractor_Interface
     */
    public function getExtractor($extractor)
    {
        // build classname
        $extractor = ucfirst($extractor);

        // attach namespace
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

        $this->extractors[$extractor] = new $class();

        $this->log('Extractor ' . $extractor . ' loaded.');

        return $this->extractors[$extractor];
    }

    /**
     * Assigns an extractor to an extension.
     *
     * @param string $extension
     * @param string $extractor
     *
     * @return null|Extractor
     */
    public function setExtractor($extension, $extractor)
    {
        // not already set
        if (false === isset($this->extractor[$extension]) and
            false === in_array($extractor, $this->extractor[$extension], true)) {
            $this->extractor[$extension][] = $extractor;
        } else { // already set

            return $this;
        }
    }

    /**
     * Removes all extractor settings.
     *
     * @return Extractor
     */
    public function removeAllExtractors()
    {
        $this->extractor = [];

        return $this;
    }

    /**
     * Saves extracted data into gettext file.
     *
     * @param string $file
     * @param array  $data
     *
     * @return Extractor
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
     * @param bool $return_as_string True, returns a string (default) and false returns an array.
     *
     * @return mixed Array or String. Returns string by default.
     */
    public static function getPOFileHeader($return_as_string = true)
    {
        $output   = [];
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

        if ($return_as_string) {
            return implode("\n", $output);
        } else { // return array

            return $output;
        }
    }

    /**
     * Formats fetched data to gettext portable object syntax.
     *
     * @param array $data
     *
     * @return string
     */
    protected function formatData($data)
    {
        $pluralMatchRegexp = '#\%([0-9]+\$)*d#';

        $output = [];
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

        return implode("\n", $output);
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