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
 *
 */

namespace Koch\Config\Exception;

/**
 * Koch Framework - The JSON Exception class represents any failures of decoding json strings.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class JsonException extends \Exception
{
    public $error = null;
    public $error_code = JSON_ERROR_NONE;

    /**
     * Constructor.
     *
     * @param $filename
     * @param $error_code
     */
    public function __construct($filename, $error_code = null)
    {
        $this->error_code = $error_code;
        $this->error = sprintf(
            _('JSON Error in file "%s". %s'),
            $filename,
            $this->getJsonErrorMessage($error_code)
        );

        parent::__construct();
    }

    /**
     * Returns the proper json error message for the given JSON Error Type.
     * $json_error_type is generated by json_decode() and fetched via json_last_error(),
     * then passed to this method.
     *
     * @param  string $json_error_type The json error type to get the error message for.
     * @return string The json error message for the given error type.
     */
    public static function getJsonErrorMessage($json_error_type = null)
    {
        if ($json_error_type === null) {
            return _('The json content from file was null.');
        }

        $json_error_messages = array(
            JSON_ERROR_DEPTH => _('The maximum stack depth has been exceeded.'),
            JSON_ERROR_STATE_MISMATCH => _('File contains invalid or malformed JSON.'),
            JSON_ERROR_CTRL_CHAR => _('Unexpected control character found, possibly invalid encoding.'),
            JSON_ERROR_SYNTAX => _('Syntax Error, malformed JSON.'),
            JSON_ERROR_UTF8 => _('Malformed UTF-8 characters, possibly invalid encoding.')
        );

        return $json_error_messages[$json_error_type];
    }

    public function __toString()
    {
        return $this->error;
    }
}
