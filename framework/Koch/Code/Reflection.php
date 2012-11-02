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

namespace Koch\Code;

/**
 * Class for Reflection and Introspection of Classes.
 *
 * Purpose of this class is to reverse-engineer classes,
 * interfaces, functions, methods and extensions.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Reflection
 */
class Reflection
{
    private $classname = '';

    /**
     * constructor
     *
     * @param string $classname
     */
    public function __construct($classname = null)
    {
        $this->setClassName($classname);
    }

    /**
     * Set the name of the class to reflect
     *
     * @param string $classname
     */
    public function setClassName($classname)
    {
        $this->classname = $classname;
    }

    /**
     * Get the name of the class to reflect
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->classname;
    }

    /**
     * Returns all methods of a class, excluding the ones specified in param.
     *
     * @param $exclude_classnames
     * @return array Methods of the class.
     */
    public function getMethods($exclude_classnames = null)
    {
        $methods_array = array();

        // if exlcude_classnames is a string, turn into array
        $exclude_classnames = (array) $exclude_classnames;

        // check if the class to reflect is available
        if (class_exists($this->classname)) {
            $class = new \ReflectionClass($this->classname);
        } else {
            throw new \RuntimeException(_('Class not existing: ' . $this->classname));
        }

        // get all methods of that class
        $methods = $class->getMethods();

        foreach ($methods as $method) {
            // get the declaring classname, might be the parent class
            $className = $method->getDeclaringClass()->getName();

            // if the classname is not excluded
            if (false === in_array($className, $exclude_classnames)) {
                // add the method name to the array
                $methods_array[$className][] = $method->getName();

                // get parameter names
                #foreach($method->getParameters() as $parameter)
                #{
                //    $parameterName = $parameter->getName();
                #}
            }
        }

        return $methods_array;
    }
}
