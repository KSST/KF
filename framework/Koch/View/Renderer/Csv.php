<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for CSV data.
 *
 * This is a wrapper/adapter for rendering CSV Data. CSV stands for 'comma-seperated-values'.
 * These files are commonly used to export and import data into different databases.
 */
class Csv extends AbstractRenderer
{
    public $headers = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

    }

    public function initializeEngine($template = null)
    {
        return;
    }

    public function configureEngine()
    {
        return;
    }

    /**
     * @param array $data   the array with the data to write as csv
     * @param array $header additional array with column headings (first row of the data)
     */
    public function assign($data, $headers = array())
    {
        $this->viewdata = $data;
        $this->headers = $headers;
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
     *                         @param array|object viewdata
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

    private function writeCSV($data, $column_headers = array(), $display = false)
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
