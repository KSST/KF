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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * View Renderer for CSV data.
 *
 * This is a wrapper/adapter for rendering CSV Data. CSV stands for 'comma-seperated-values'.
 * These files are commonly used to export and import data into different databases.
 */
class Csv extends AbstractRenderer
{
    public $headers = [];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
    }

    public function initializeEngine($template = null)
    {
        // @todo
    }

    public function configureEngine()
    {
        // @todo
    }

    /**
     * @param array    $data    the array with the data to write as csv
     * @param string[] $headers additional array with column headings (first row of the data)
     */
    public function assign($data, $headers = [])
    {
        $this->viewdata = $data;
        $this->headers  = $headers;
    }

    public function display($template, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        return $this->writeCSV($this->viewdata, $this->headers, false);
    }

    public function fetch($template, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        return $this->writeCSV($this->viewdata, $this->headers, true);
    }

    /**
     * @param string $template The filepath location of where to save the csv file.
     * @param array|object viewdata
     */
    public function render($template = null, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        $renderToFile = (empty($template) === false) ? true : false;

        if ($renderToFile === true) {
            $return = $this->writeCSV($this->viewdata, $this->headers, true);

            return (bool) file_put_contents($template, $return);
        }

        return $this->writeCSV($this->viewdata, $this->headers);
    }

    private function writeCSV($data, $column_headers = [], $display = false)
    {
        $stream = ($display === true) ? fopen('php://temp/maxmemory', 'w+') : fopen('php://output', 'w');

        if (empty($column_headers) === false) {
            fputcsv($stream, $column_headers);
        }

        foreach ($data as $record) {
            fputcsv($stream, $record);
        }

        if ($display === true) {
            rewind($stream);
            $retVal = stream_get_contents($stream);
            fclose($stream);

            return $retVal;
        } else {
            fclose($stream);
        }
    }
}
