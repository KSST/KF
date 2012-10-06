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

namespace "Koch\Form\Element\" + formelement name
        $class = 'Koch\Form\Elements\\' . ucfirst($formelement);

        // if not already loaded, require formelement file
        if (false === class_exists($class, false)) {
            $file = KOCH_FRAMEWORK . 'form/elements/' . $formelement . '.php';

            if (is_file($file) === true) {
                include $file;
            } else {
                throw new \Exception('The Formelement "' . $class . '" does not exist.');
            }
        }

        // instantiate the new formelement and return
        return new $class;
    }

    /**
     * ===================================================================================
     *      Form Processing
     * ===================================================================================
     */

    /**
     * processForm
     *
     * This is the main formular processing loop.
     * If the form does not validate, then redisplay it,
     * else present "Success"-Message!
     */
    public function processForm()
    {
        // check if form has been submitted properly
        if ($this->validate() === false) {
            // if not, redisplay the form (decorate with errors + render)
            $this->addDecorator('errors');
            $this->render();

        } else { // form was properly filled, display a success web page or a flashmessage
            /**
             * Success - form content valid.
             * The "noerror" decorator implementation decides,
             * if a success web page or a flashmessage is used.
             */
            $this->addDecorator('noerror');
            $this->render();
        }
    }

    /**
     * Get the data array
     *
     * @return array containing all the form data.
     */
    protected function bind()
    {

    }

    /**
     * Set Values to Form
     *
     * An associative array is used to pre-populate form elements.
     * The keys of this array correspond with the element names.
     *
     * There are two use cases for this method:
     * 1) pre-filled form
     *    Some default values are set to the form, which then get altered by the user.
     * b) incomming post data
     *    Set the incomming POST data values are set to the form for validation.
     *
     * @param object|array $data Object or Array. If null (default), POST parameters are used.
     */
    public function setValues($data = null)
    {
        // because $data might be an object, typecast $data object to array
        if (is_object($data) === true) {
            $data = (array) $data;
        }
        // fetch data from POST
        elseif (null === $data) {
            if ('POST' === Koch_HttpRequest::getRequestMethod() ) {
                $data = Koch_HttpRequest::getPost();
            }
        }

        // now we got an $data array to populate all the formelements with (setValue)
        foreach ($data as $key => $value) {
            foreach ($this->formelements as $formelement) {
                $type = $formelement->getType();

                /**
                 * Exclude some formelements from setValue(), e.g. buttons, etc.
                 * Setting the value would just change the visible "name" of these elements.
                 */
                if (true === in_array($type, array('submit', 'button', 'cancelbutton', 'resetbutton'))) {
                    continue;
                }

                // data[key] and formelement[name] have to match
                if ($formelement->getName() == ucfirst($key)) {
                    $formelement->setValue($value);
                }
            }
        }
    }

    /**
     * Get all values of the form.
     *
     * Or a bit more exact:
     * Get an array with the values of all the formelements objects which are registered to the form object.
     * The values are validated and ready for further processing, e.g. insert to model object.
     *
     * The validation is the big difference between using the $_POST array directly or indirectly.
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();

        foreach ($this->formelements as $formelement) {
            /**
             * Create an associative array $value[id] => value
             */
            $values[$formelement->getId()] = $formelement->getValue();
        }

        // return validated values, ready for further processing (model insert)
        return $values;
    }

    /**
     * ===================================================================================
     *      Form Decoration
     * ===================================================================================
     */

    /**
     * Is a shortcut/proxy/convenience method for addDecorator()
     * <strong>WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM</strong>
     *
     * @see $this->addDecorator()
     *
     * @param  array              $decorators Array of decorator objects or names or just one string.
     * @param  array              $attributes Array of properties for the decorator object.
     * @return Koch_Formdecorator object
     */
    public function setDecorator($decorators, $attributes = null)
    {
        return $this->addDecorator($decorators, $attributes);
    }

    /**
     * Add multiple decorators at once
     *
     * @param array $decorators Array of decorator objects or names.
     */
    public function addDecorators($decorators)
    {
        // address each one of those decorators
        foreach ($decorators as $decorator) {
            $this->addDecorator($decorator);
        }
    }

    /**
     * Adds a decorator to the form
     * <strong>WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM</strong>
     *
     * @example
     * $form->addDecorator('fieldset')->setLegend('legendname');
     *
     * @param  array              $decorator  Array of decorator objects or names or just one string.
     * @param  array              $attributes Array of properties for the decorator object.
     * @return Koch_Formdecorator object
     */
    public function addDecorator($decorator, $attributes = null)
    {
        // check if multiple decorator are incomming at once
        if (is_array($decorator)) {
            $this->addDecorators($decorator);
        }

        // if we got a string
        if (is_string($decorator)) {
            // turn string into an decorator object
            $decorator = $this->decoratorFactory($decorator);
        }

        // and check if it is an object implementing the right interface
        if ($decorator instanceof \Koch\Form\DecoratorInterface) {
            // if so, fetch this decorator objects name
            $decoratorname = '';
            $decoratorname = $decorator->name;
        }

        // apply attributes (2nd param) to the decorator
        if ($attributes !== null) {
            foreach ($attributes as $attribute => $value) {
                $decorator->$attribute = $value;
            }
            #$decorator->setDecoratorAttributesArray($attributes);
            #\Koch\Debug::printR($decorator);
        }

        // now check if this decorator is not already set (prevent decorator duplications)
        if (false === in_array($decorator, $this->formdecorators)) {
            // set this decorator object under its name into the array
            $this->formdecorators[$decoratorname] = $decorator;
        }

        // WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM
        // We dont return $this here, because $this would be the FORM.
        // Instead the decorator is returned, to apply some properties.
        // @return decorator object
        return $this->formdecorators[$decoratorname];
    }

    /**
     * Getter Method for the formdecorators
     *
     * @return array with registered formdecorators
     */
    public function getDecorators()
    {
        return $this->formdecorators;
    }

    /**
     * Toggles the Usage of Default Form Decorators
     * If set to false, registerDefaultFormDecorators() is not called during render()
     *
     * @see render()
     * @see registerDefaultFormDecorators()
     *
     * @param type $boolean Form is decorated on true (default), not decorated on false.
     */
    public function useDefaultFormDecorators($boolean = true)
    {
        $this->useDefaultFormDecorators = $boolean;
    }

    /**
     * Set default form decorators (form)
     */
    public function registerDefaultFormDecorators()
    {
        $this->addDecorator('html5validation');
        $this->addDecorator('form');
        $this->addDecorator('fieldset');
        $this->addDecorator('div')->setId('forms');
    }

    /**
     * Removes a form decorator from the decorator stack by name or object.
     *
     * @param mixed|string|object $decorator Object or String identifying the Form Decorator.
     */
    public function removeDecorator($decorator)
    {
        // check if it is an object implementing the right interface
        if ($decorator instanceof \Koch\Form\DecoratorInterface) {
            // if so, fetch this decorator objects name
            // and overwrite $decorator variable containing the object
            // with the decorator name string
            $decorator = (string) $decorator->name;
        }

        // here variable $decorator must be string
        if (array_key_exists($decorator, $this->formdecorators)) {
            unset($this->formdecorators[$decorator]);
        }
    }

    public function getDecorator($decorator)
    {
        if (isset($this->formdecorators[$decorator]) === true) {
            return $this->formdecorators[$decorator];
        } else {
           throw new \Exception('The Form does not have a Decorator called "' . $decorator . '".');
        }
    }

    /**
     * Factory method. Instantiates and returns a new formdecorator object.
     *
     * @param string Name of Formdecorator.
     * @return Koch_Formdecorator
     */
    public function decoratorFactory($decorator)
    {
        // this matches 0-9, when the next char is a-z (lookahead) followed by an [a-z]
        // html5validation, '5v' is match[0], strtoupper will '5V'
        // turining html5validation into html5Validation, in the next step a ucfirst is applied
        $decorator = preg_replace('/([0-9])(?=[a-z])([a-z])/e', 'strtoupper("$0");', $decorator);

        // construct Koch\Form\Decorator\Name
        $class = 'Koch\Form\Decorators\Form\\' . ucfirst($decorator);

        // if not already loaded, require forelement file
        if (false === class_exists($class, false)) {
            $file = KOCH_FRAMEWORK . 'Form/Decorators/Form/' . $decorator . '.php';

            if (is_file($file) === true) {
                include $file;
            }
        }

        // instantiate the new $formdecorator and return
        return new $class();
    }

     /**
     * Sets the Decorator Attributes Array
     *
     * Decorators are not instantiated at the time of the form definition via an array.
     * So configuration can only be applied indirtly to these objects.
     * The values are keept in this array and are autmatically applied, when rendering the form.
     *
     * @return array decoratorAttributes
     */
    public function setDecoratorAttributesArray(array $attributes)
    {
        $this->decoratorAttributes = $attributes;
    }

    /**
     * Returns the Decorator Attributes Array
     *
     * Decorators are not instantiated at the time of the form definition via an array.
     * So configuration can only be applied indirtly to these objects.
     * The values are keept in this array and are autmatically applied, when rendering the form.
     *
     * @return array decoratorAttributes
     */
    public function getDecoratorAttributesArray()
    {
        return $this->decoratorAttributes;
    }

    /**
     * Array Structure
     *
     * $decorator_attributes = array(
     *  Level 1 - key = decorator type
     *  'form'  => array (
     *              Level 2 - key = decorator name
     *             'fieldset' => array (
     *                   Level 3 - key = attribute name and value = mixed(string|int)
     *                  'description' =>  'description test')
     *                  )     *
     *  'formelement' = array ( array() )
     * );
     * form => array ( fieldset => array( description => description text ) )
     */
    public function applyDecoratorAttributes()
    {
        $attributes = (array) $this->decoratorAttributes;

        #\Koch\Debug::printR($attributes);

        // level 1
        foreach ($attributes as $decorator_type => $decoratorname_array) {
            // apply settings for the form itself
            if ($decorator_type === 'form') {
                // level 2
                foreach ($decoratorname_array as $decoratorname => $attribute_and_value) {
                    $decorator = $this->getDecorator($decoratorname);
                    #\Koch\Debug::printR($attribute_and_value);

                    // level 3
                    foreach ($attribute_and_value as $attribute => $value) {
                        $decorator->$attribute = $value;
                    }
                    #\Koch\Debug::printR($decorator);
                }
            }

            // apply settings to a formelement of the form
            if ($decorator_type === 'formelement') {
                // level 2
                foreach ($decoratorname_array as $decoratorname => $attribute_and_value) {
                    $decorator = $this->getFormelementDecorator($decoratorname);
                    #\Koch\Debug::printR($attribute_and_value);

                    // level 3
                    foreach ($attribute_and_value as $attribute => $value) {
                        $decorator->$attribute = $value;
                    }
                }
            }
        }

        unset($attributes, $this->decoratorAttributes);
    }

    /**
     * ===================================================================================
     *      Formelement Decoration
     * ===================================================================================
     */

    /**
     * setFormelementDecorator
     *
     * Is a shortcut/proxy/convenience method for addFormelementDecorator()
     * @see $this->addFormelementDecorator()
     *
     * WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM
     * @return Koch_Formdecorator object
     */
    public function setFormelementDecorator($decorator, $formelement_position = null)
    {
        return $this->addFormelementDecorator($decorator, $formelement_position);
    }

    /**
     * Adds a decorator to a formelement.
     *
     * The first parameter accepts the formelement decorator.
     * You might specify a decorater
     * (a) by its name or
     * (b) multiple decorators as an array or
     * (c) a instantied decorator object might me handed to this method.
     * @see addDecorator()
     *
     * The second parameter specifies the formelement_position.
     * If no position is given, it defaults to the last formelement in the stack of formelements.
     *
     * <strong>WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM</strong>
     *
     * @example
     * $form->addFormelementDecorator('fieldset')->setLegend('legendname');
     * This would attach the decorator fieldset to the last formelement of $form.
     *
     * @param  string|array|object $decorator            The formelement decorator(s) to apply to the formelement.
     * @param  int|string|object   $formelement_position Position in the formelement stack or Name of formelement.
     * @return Koch_Formdecorator  object
     */
    public function addFormelementDecorator($decorator, $formelement_pos_name_obj = null)
    {
        if (is_array($this->formelements) === false) {
            throw new \Exception('No Formelements found. Add the formelement first, then decorate it!');
        }

        $formelement_object = '';

        if (false === is_object($formelement_pos_name_obj)) {
            $formelement_object = $this->getElement($formelement_pos_name_obj);
        }

        // add the decorator
        // WATCH OUT! this is a forwarding call to formelement.core.php->addDecorator()
        return $formelement_object->addDecorator($decorator);
    }

    public function removeFormelementDecorator($decorator, $formelement_position = null)
    {
        $formelement_object = '';
        $formelement_object = $this->getElement($formelement_position);

        if ($formelement_object->formelementdecorators[$decorator] !== null) {
            return $formelement_object->formelementdecorators[$decorator];
        }
    }

    /**
     * ===================================================================================
     *      Form Groups
     * ===================================================================================
     */

    /**
     * Adds a new group to the form, to group one or several formelements inside.
     *
     * @return Koch_Form
     */
    /*
    public function addGroup($groupname)
    {
        // @todo groupname becomes ID of decorator (e.g. a fieldset)

        $this->formgroups[] = $groupname;

        return $this;
    }*/

    /**
     * ===================================================================================
     *      Form Validation
     * ===================================================================================
     */

    /**
     * Adds a validator to the formelement
     *
     * @return Koch_Formelement
     */
    public function addValidator($validator)
    {
        if (is_object($validator) and is_a($validator, Koch\Form\ValidatorInterface)) {

        }

        return $this;
    }

    /**
     * Validates the form.
     *
     * The method iterates (loops over) all formelement objects and calls the validation on each object.
     * In other words: a form is valid, if all formelement are valid. Surprise, surprise.
     *
     * @return boolean Returns true if form validates, false if validation fails, because errors exist.
     */
    public function validateForm()
    {
        foreach ($this->formelements as $formelement) {
            if ($formelement->validate() === false) {
                // raise error flag on the form
                $this->setErrorState(true);

                // and transfer errormessages from formelement to form errormessages stack
                $this->addErrorMessage($formelement->getErrorMessages());
            }
        }

        if ($this->getErrorState() === true) {
            // form has errors and does not validate
            return false;
        } else {
            return true;
        }
    }

    /**
     * ===================================================================================
     *      Form Errormessages
     * ===================================================================================
     */

     /**
      * Sets the error state of the form (formHasError).
      *
      * @param boolean $boolean
      */
     public function setErrorState($boolean = true)
     {
        $this->error = $boolean;
     }

     /**
      * Returns the error state of the form.
      *
      * @return boolean False, if form has an error. True, otherwise.
      */
     public function getErrorState()
     {
        return $this->error;
     }

     public function addErrorMessage($errormessage)
     {
        $this->errormessages[] = $errormessage;
     }

     public function addErrorMessages(array $errormessages)
     {
        $this->errormessages = $errormessages;
     }

     public function resetErrormessages()
     {
        $this->errormessages = array();
     }

     public function getErrormessages()
     {
        return $this->errormessages;
     }

    /**
     * ============================
     *    Magic Methods: get/set
     * ============================
     */

    /**
     * Magic Method: set
     *
     * @param $name Name of the attribute to set to the form.
     * @param $value The value of the attribute.
     */
    public function __set($name, $value)
    {
        $this->setAttributes(array($name => $value));
    }

    /**
     * Magic Method: get
     *
     * @param $name
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
}
