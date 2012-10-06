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

namespace Koch\Config\Adapter;

/**
 * Config Handler for CSV Format.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class CSV
{
    /**
     * Read the config array from csv file
     *
     * @param   string  The filename
     * @return mixed array | bool false
     */
    public static function readConfig($file)
    {
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \Koch\Exception\Exception('CSV Config File not existing or not readable.');
        }

        $csvarray = array();

        // read file
        if (($handle = fopen($file, "r+")) !== false) {
            // set the parent multidimensional array key to 0
            $key = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // count the total keys in the row
                $c = count($data);

                // populate the multidimensional array
                for ($x = 0; $x < $c; $x++) {
                    $csvarray[$key][$x] = $data[$x];
                }
                $key++;
            }

            // close the File.
            fclose($handle);
        }

        return $csvarray;
    }

    /**
     * Write the config array to csv file
     *
     * @param   string  The filename
     * @param   array   The configuration array
     */
    public function writeConfig($file, array $array)
    {
        if (($handle = fopen($file, "r+")) !== false) {
            // transform array to csv notation
            foreach ($array as $key => $value) {
                if (is_string($value)) {
                    $value = explode(',', $value);
                    $value = array_map('trim', $value);
                }
            }

            // write to csv to file
            return fputcsv($handle, $value, ',', '"');
        }
    }
}
