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

namespace Koch\Form\Generator;

use Koch\Form\Form;
use Koch\Form\FormGeneratorInterface;

/**
 * Form Generator via Doctrine Record.
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
