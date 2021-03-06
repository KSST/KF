<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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

namespace Koch\Form\Generator;

use Koch\Form\Form;
use Koch\Form\FormGeneratorInterface;

/**
 * Koch Framework - Form Generator via Doctrine Record.
 *
 * Purpose: automatic form generation from doctrine records/tables.
 *
 * @todo determine and set excluded columns (maybe in record?)
 */
class Doctrine extends Form implements FormGeneratorInterface
{
    /**
     * The typeMap is an array of all doctrine column types.
     * It maps the database fieldtypes to their related html inputfield types.
     *
     * @var array
     */
    protected $typeMap = [
            'boolean'   => 'checkbox',
            'integer'   => 'text',
            'float'     => 'text',
            'decimal'   => 'string',
            'string'    => 'text',
            'text'      => 'textarea',
            'enum'      => 'select',
            'array'     => null,
            'object'    => null,
            'blob'      => null,
            'clob'      => null,
            'time'      => 'text',
            'timestamp' => 'text',
            'date'      => 'text',
            'gzip'      => null,
    ];

    /**
     * Database columns which should not appear in the form.
     *
     * @var array
     */
    protected $excludedColumns = [];

    /**
     * Generates a Form from a Table.
     *
     * @param string $DoctrineTableName Name of the Doctrine Tablename to build the form from.
     */
    public function generateFormByTable($DoctrineTableName)
    {
        // init form
        $form = [];

        // fetch doctrine table by record name
        $table = self::getTable($DoctrineTableName);

        // fetch all columns of that table
        $tableColumns = $table->getColumnNames();

        // loop over all columns
        foreach ($tableColumns as $columnName) {
            // => $columnType
            // and check wheather the $columnName is to exclude
            if (in_array($columnName, $this->excludeColumns, true)) {
                // stop the foreach-loop here and reenter it
                continue;
            }

            // combine classname and columnname as fieldname
            $fieldName = $table->getClassnameToReturn() . '[$columnName]';

            // if columnname is identifier
            if ($table->isIdentifier($columnName)) {
                // add it as an hidden field
                #$form[] = new Koch_Form->formfactory( 'hidden', $fieldName);
            } else {
                // transform columnName to a printable name
                $printableName = ucwords(str_replace('_', '', $columnName));

                // determine the columnname type and add the formfield
                #$form[] = new Koch_Form->formfactory( $table->getTypeOf($columnName), $fieldName, $printableName);
            }
        }

        return $form;
    }

    /**
     * Facade/Shortcut.
     */
    public function generate($array)
    {
        $this->generateFormByTable($array);
    }
}
