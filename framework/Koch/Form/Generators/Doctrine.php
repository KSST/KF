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

namespace Koch\Form\Generators;

/**
 * Koch Framework - Form Generator via Doctrine Record
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
    protected $typeMap = array(
            'boolean'    => 'checkbox',
            'integer'    => 'text',
            'float'      => 'text',
            'decimal'    => 'string',
            'string'     => 'text',
            'text'       => 'textarea',
            'enum'       => 'select',
            'array'      => null,
            'object'     => null,
            'blob'       => null,
            'clob'       => null,
            'time'       => 'text',
            'timestamp'  => 'text',
            'date'       => 'text',
            'gzip'       => null
    );

    /**
     * Database columns which should not appear in the form.
     *
     * @var array
     */
    protected $excludedColumns   = array();

    /**
     * Generates a Form from a Table
     *
     * @param string $DoctrineTableName Name of the Doctrine Tablename to build the form from.
     */
    public function generateFormByTable($DoctrineTableName)
    {
        // init form
        $form = array();

        // fetch doctrine table by record name
        $table = Doctrine::getTable($DoctrineTableName);

        // fetch all columns of that table
        $tableColumns = $table->getColumnNames();

        // loop over all columns
        foreach ( $tableColumns as $columnName) // => $columnType

        {
            // and check wheather the $columnName is to exclude
            if (in_array($columnName, $this->excludeColumns)) {
                // stop the foreach-loop here and reenter it
                continue;
            }

            // combine classname and columnname as fieldname
            $fieldName = $table->getClassnameToReturn() . '[$columnName]';

            // if columnname is identifier
            if ( $table->isIdentifier($columnName) ) {
                // add it as an hidden field
                #$form[] = new Koch_Form->formfactory( 'hidden', $fieldName);
            } else {
                // transform columnName to a printable name
                $printableName = ucwords(str_replace('_','',$columnName));

                // determine the columnname type and add the formfield
                #$form[] = new Koch_Form->formfactory( $table->getTypeOf($columnName), $fieldName, $printableName);
            }
        }

        return $form;
    }

    /**
     * Facade/Shortcut
     */
    public function generate($array)
    {
        $this->generateFormByTable($array);
    }
}
