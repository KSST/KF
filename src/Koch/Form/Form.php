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

namespace Koch\Form;

/**
 * Class for creation and handling of HTML Forms.
 *
 * The Formular Object provides methods to deal with the following problem:
 *
 * Normally you would define your form on html side. When the form gets submitted
 * you would perform a server-side validation on the incomming formdata against
 * certain validation rules. If your system is one of the better ones, you would
 * add these validations also on client-side as an useability enhancement.
 *
 * Problem:
 * This means that you have to implement the validation rules and validation methods two times.
 * One time via javascript on client-side, one time via php on server-side.
 *
 * Koch Framework's formhandling abstracts the form generation and solves the problem described above.
 *
 * The formular handling process can be described as the following:
 *
 * 1) Formcreation
 *    The formular is defined/described only one-time in xml (Data-Dictionary).
 *
 *    The form-definition/description contains:
 *    a) Elements
 *    b) Attributes
 *    c) Validation rules
 *
 * 2) Transformation / Generation
 *    The formular definition is then transformed into a valid html/xhtml/xml document segment
 *    with client-side validation rules and methods applied.
 *
 *    The form contains:
 *    a) Formular
 *    b) Client-side formular validation rules
 *    c) Client-side formular validation methods
 *
 * 3) The generated form is ready for getting embedded into the template/document providing the formular.
 *    The form element represents a collection of form-associated elements, some of which can represent
 *    editable values that can be submitted to a server for processing.
 *
 * Form Workflow
 *
 *    a) Embed formular
 *       -> Perform client-side validation while data is collected from user
 *       -> If validation is ok:
 *    b) Submit
 *       -> Perform server-side validation on incomming data
 *       -> If validation is ok:
 *          -> Save Data !
 *       -> Else
 *    c) Repopulate formfields on submission error
 *       -> goto a)
 *
 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/forms.html
 */
class Form implements FormInterface, \Stringable
{
    /**
     * Contains all formelements / formobjects registered for this form.
     *
     * @var array
     */
    protected $formelements = [];

    /**
     * Form attributes:.
     *
     * accept-charset, action, autocomplete, enctype, method, name, novalidate, target
     *
     * @link http://dev.w3.org/html5/html-author/#forms
     */

    /**
     * Contains accept-charset of the form.
     *
     * @var string
     */
    protected $acceptcharset;

    /**
     * Contains action of the form.
     *
     * @var string
     */
    protected $action;

    /**
     * Contains autocomplete state of the form.
     *
     * @var bool
     */
    protected $autocomplete;

    /**
     * Contains encoding of the form.
     *
     * @var string
     */
    protected $encoding;

    /**
     * Contains action of the form.
     *
     * @var string
     */
    protected $method;

    /**
     * Contains action of the form.
     *
     * @var string
     */
    protected $name;
    protected $noValidation;
    protected $target;

    /**
     * Contains id of the form.
     *
     * @var string
     */
    protected $id;

    /**
     * Contains class of the form.
     *
     * @var string
     */
    protected $class;

    /**
     * Contains description of the form.
     *
     * @var string
     */
    protected $description;

    /**
     * Contains heading of the form.
     *
     * @var string
     */
    protected $heading;

    /**
     * Flag variable to indicate, if form has an error.
     *
     * @var bool
     */
    protected $error = false;

    /**
     * Form Decorators Array, contains one or several formdecorator objects.
     *
     * @var array
     */
    private $formdecorators = [];

    /**
     * Toogle variable to control registering of default Formdecorators during rendering.
     *
     * @var bool
     */
    private $useDefaultFormDecorators = true;

    /**
     * Form Groups Array, contains one or several formgroup objects.
     *
     * @var array
     */
    protected $formgroups = [];

    /**
     * Errormessages Stack.
     *
     * @var array
     */
    protected $errorMessages = [];

    /**
     * Construct.
     *
     * @example
     * $form = Koch_Form('news_form', 'post', 'index.php?mod=news&sub=admin&action=update&type=create');
     *
     * @param mixed|array|string $name_or_attributes Set the name of the form OR and array with attributes.
     * @param string             $method             Set the method of the form. Valid are get/post.
     * @param string             $action             Set the action of the form.
     */
    public function __construct($name_or_attributes = null, $method = null, $action = null)
    {
        if (null === $name_or_attributes) {
            throw new \InvalidArgumentException(
                'Missing argument 1. Expected a string (Name of Form) or an array (Form Description Array).'
            );
        }

        if (is_string($name_or_attributes)) {
            // case 1: $name is a string, the name of the form
            $this->setName($name_or_attributes);
        } elseif (is_array($name_or_attributes)) {
            // case 2: $name is an array with several attribute => value relationships
            $this->setAttributes($name_or_attributes);
        }

        if ($method !== null and $action !== null) {
            $this->setMethod($method);
            $this->setAction($action);
        }
    }

    /**
     * Sets the method (POST, GET) to the form.
     *
     * @param string $method POST or GET
     *
     * @return Form
     */
    public function setMethod($method)
    {
        if ($method === 'POST' or $method === 'GET') {
            $this->method = $method;
        } else {
            throw new \InvalidArgumentException(
                _('The method parameter is "' . $method . '", but has to be GET or POST.')
            );
        }

        return $this;
    }

    /**
     * Returns method (GET or POST) of this form.
     *
     * @return string Name of the method of this form. Defaults to POST.
     */
    public function getMethod()
    {
        // defaults to post
        if ($this->method === null) {
            $this->method = 'POST';
        }

        return $this->method;
    }

    /**
     * Set action of this form (which is the target url).
     *
     * @param string $action string Target URL of the action of this form.
     *
     * @return Form
     */
    public function setAction($action)
    {
        $this->action = \Koch\Router\Router::buildURL($action);

        return $this;
    }

    /**
     * Returns action of this form (target url).
     *
     * @return string Target Url as the action of this form.
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the auto-completion state of this form.
     *
     * @return string Returns the auto-completion state of this form.
     */
    public function isAutoComplete()
    {
        return ($this->autocomplete === true) ? 'on' : 'off';
    }

    /**
     * Set autocomplete of this form.
     * If "on" browsers can store the form's input values, to auto-fill the form if the user returns to the page.
     *
     * @param bool $bool boolean state to set for autocomplete.
     *
     * @return Form
     */
    public function setAutoComplete($bool)
    {
        $this->autocomplete = (bool) $bool;

        return $this;
    }

    /**
     * Gets the target (_blank, _self, _parent, _top).
     *
     * @return string string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the target of the form.
     *
     * _blank    Open in a new window
     * _self      Open in the same frame as it was clicked
     * _parent  Open in the parent frameset
     * _top
     *
     * @param string $target _blank, _self, _parent, _top
     *
     * @return Form
     */
    public function setTarget($target)
    {
        if ($target === '_blank' or $target === '_self' or $target === '_parent' or $target === '_top') {
            $this->target = $target;
        } else {
            throw new \InvalidArgumentException(
                'The target parameter is "' . $target . '", but has to be one of _blank, _self, _parent, _top.'
            );
        }

        return $this;
    }

    /**
     * Returns novalidation state of this form.
     * If present the form should not be validated when submitted.
     *
     * @return string Returns novalidation state of this form.
     */
    public function isNoValidation()
    {
        return ($this->noValidation === true) ? 'novalidate' : '';
    }

    /**
     * Set novalidation state of this form.
     * If true the form should not be validated when submitted.
     *
     * @link http://dev.w3.org/html5/spec-author-view/association-of-controls-and-forms.html#attr-fs-novalidate
     *
     * @param bool $bool boolean state to set for novalidation.
     *
     * @return Form
     */
    public function setNoValidation($bool)
    {
        $this->noValidation = (bool) $bool;

        return $this;
    }

    /**
     * Returns the requested attribute if existing else null.
     *
     * @param $parametername
     *
     * @return mixed null or value of the attribute
     */
    public function getAttribute($attributename)
    {
        if (isset($this->{$attributename})) {
            return $this->{$attributename};
        } else {
            return;
        }
    }

    /**
     * Setter method for Attribute.
     *
     * @param array $attribute attribute name
     * @param array $value     value
     */
    public function setAttribute($attribute, $value)
    {
        $this->{$attribute} = $value;
    }

    /**
     * Setter method for Attributes.
     *
     * @param array $attributes Array with one or several attributename => value relationships.
     */
    public function setAttributes($attributes)
    {
        if (is_array($attributes)) {
            /*
             * The incomming attributes array contains a form description array for the formgenerator.
             */
            if (isset($attributes['form'])) {
                // generate form
                $form = new \Koch\Form\Generator\PHPArray($attributes);
                // copy all properties of the inner form object to ($this) outer form object =)
                $this->copyObjectProperties($form, $this);
                // unset inner form
                unset($form);
            } else {
                /*
                 * Just normal <form attribute(s)=value></form>
                 */
                foreach ($attributes as $attribute => $value) {
                    $this->setAttribute($attribute, $value);
                }
            }
        }
    }

    /**
     * Copy properties from object A to object B.
     *
     * @param object $object_to_copy The Object to copy the properties from.
     * @param object $target         The Object to copy the properties to. Defaults to $this.
     */
    public function copyObjectProperties($object_to_copy, $target = null)
    {
        $varArray = get_object_vars($object_to_copy);

        foreach ($varArray as $key => $value) {
            // use this object, if no target object is specified
            if ($target === null) {
                $this->$key = $value;
            } else {
                $target->$key = $value;
            }
        }

        unset($key, $value);
    }

    /**
     * Set id of this form.
     *
     * @param string $id ID of this form.
     *
     * @return Form
     */
    public function setID($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns action of this form.
     *
     * @return string ID of this form.
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Set name of this form.
     *
     * @param string $name Name of this form.
     *
     * @return Form
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns name of this form.
     *
     * @return string Name of this form.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set accept-charset of this form.
     * Like accept-charset="ISO-8859-1".
     *
     * @param string $charset Charset of this form (utf-8, iso-8859-1).
     *
     * @return Form
     */
    public function setAcceptCharset($charset)
    {
        $this->acceptcharset = $charset;

        return $this;
    }

    /**
     * Returns accept-charset of this form.
     *
     * @return string Accept-charset of this form. Defaults to UTF-8.
     */
    public function getAcceptCharset()
    {
        if (empty($this->acceptcharset)) {
            $this->setAcceptCharset('utf-8');
        }

        return $this->acceptcharset;
    }

    /**
     * Set class of this form.
     *
     * @param string $class Css Classname of this form.
     *
     * @return Form
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Returns css classname of this form.
     *
     * @return string Css Classname of this form.
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the description text of this form.
     * The description is a p tag after the heading (form > h2 > p).
     *
     * @param string $description Description of this form.
     *
     * @return Form
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns class of this form.
     *
     * @return string Description of this form.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set a heading for this form.
     * The heading is a h2 tag directly after the opening form tag.
     *
     * @param string $heading Heading of this form.
     *
     * @return Form
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Returns heading of this form.
     *
     * @return string Heading of this form.
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Shortcut to set the Legend text of the fieldset decorator.
     *
     * The legend tag belongs to the fieldset decorator.
     * The fieldset decorator is a default decorator instantiated, when rendering the form.
     * It does not exist at the time of form definition.
     * So we keep the legend value stored, till the fieldset decorator is instantiated.
     * Then the decorator attributes array is automatically assigned to the form and it's objects.
     *
     * Note: you can use the long form (array notation) anytime, when defining your form.
     * Though using method chaining is a bit nicer (fluent interface).
     *
     * @param string String for the legend tag of the fieldset.
     * @param string $legend
     *
     * @return Form Koch_Form
     */
    public function setLegend($legend)
    {
        $this->setDecoratorAttributesArray(['form' => ['fieldset' => ['legend' => $legend]]]);

        return $this;
    }

    public function getLegend()
    {
        return $this->decoratorAttributes['form']['fieldset']['legend'];
    }

    /**
     * Set encoding type of this form.
     *
     * - application/x-www-form-urlencoded
     *  All characters are encoded before sent (this is default)
     * - multipart/form-data
     *  No characters are encoded.
     *  This value is required when you are using forms that have a file upload control
     * - text/plain
     *  Spaces are converted to "+" symbols, but no special characters are encoded
     *
     * @param string $encoding Encoding type of this form.
     *
     * @return Form
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Returns encoding type of this form.
     *
     * @return string Encoding type of this form.
     */
    public function getEncoding()
    {
        if (empty($this->encoding)) {
            $this->encoding = 'multipart/form-data';

            return $this->encoding;
        } else {
            return $this->encoding;
        }
    }

    /**
     * Getter for formelements array.
     *
     * @return array Formelements
     */
    public function getFormelements()
    {
        return $this->formelements;
    }

    /**
     * Set formelements.
     *
     * @param string[] $formelements
     */
    public function setFormelements(array $formelements)
    {
        $this->formelements = $formelements;
    }

    /**
     * ===================================================================================
     *      Form Errors
     * ===================================================================================.
     */

    /**
     * Get the form error status.
     *
     * @return bool
     */
    public function formHasErrors()
    {
        return $this->error;
    }

    /**
     * ===================================================================================
     *      Render
     * ===================================================================================.
     */

    /**
     * Registers the default decorators for a formelement.
     * The default decorators are: label, description, div.
     *
     * @param object $formelement \Koch\Form\Element\Interface
     */
    public function registerDefaultFormelementDecorators($formelement)
    {
        $formelement->addDecorator('label');
        $formelement->addDecorator('description');
        $formelement->addDecorator('div')->setCssClass('formline');
    }

    /**
     * Renders all fromelements.
     *
     * @return string HTML of Formelements.
     */
    public function renderAllFormelements()
    {
        $html_form        = '';
        $html_formelement = '';

        // fetch all formelements
        $formelements = $this->getFormelements();

        #\Koch\Debug::printR($formelements);
        // developer hint: when $form->render() was triggered, but no formelement was added before
        if (count($formelements) === 0) {
            throw new \Koch\Exception\Exception(
                _('Error rendering formelements. ') .
                _('No formelements on form object. Consider adding some formelements using addElement().')
            );
        }

        // sort formelements by index
        ksort($formelements);

        // loop over all registered formelements of this form and render them
        foreach ($formelements as $formelement) {
            // fetch all decorators of this formelement
            $formelementdecorators = $formelement->getDecorators();

            /*
             * Do not add default formelement decorators
             * 1) if some were already added manually
             * 2) if the feature is disabled (setting is then incomming from inside the formelement)
             */
            if (empty($formelementdecorators) && ($formelement->disableDefaultDecorators === false)) {
                // apply default decorators to the formelement
                $this->registerDefaultFormelementDecorators($formelement);

                // fetch again all decorators of this formelement
                $formelementdecorators = $formelement->getDecorators();
            }

            // then render this formelement
            $html_formelement = $formelement->render();

            // for each decorator, decorate the formelement and render it
            foreach ($formelementdecorators as $formelementdecorator) {
                $formelementdecorator->decorateWith($formelement);
                $html_formelement = $formelementdecorator->render($html_formelement);
            }

            // append the form html with the decorated formelement html
            $html_form .= $html_formelement;
        }

        #\Koch\Debug::printR($html_form);

        return $html_form;
    }

    /**
     * Render this form.
     *
     * @return Koch_Formelement
     */
    public function render()
    {
        // a) the content of the form are all the formelements
        $html_form = $this->renderAllFormelements();

        // b) attach default decorators
        //if (empty($this->formdecorators)) {
            // should the default form decorators be applied?
            if ($this->useDefaultFormDecorators === true) {
                // set a common style to the form by registering one or more decorators
                $this->registerDefaultFormDecorators();
            }
        //}

        // iterate over all decorators
        foreach ($this->getDecorators() as $decorator) {
            // stick form into the decorator decorator
            $decorator->decorateWith($this);

            // apply some settings or call some methods on the decorator
            // before rendering $decorator->$value; or $decorator->$method($value);
            // combined into $decorator->setAttributes();
            $this->applyDecoratorAttributes();

            $html_form = $decorator->render($html_form);

            // remove the processed decorator from the decorators stack
            $this->removeDecorator($decorator);

            // unset decorator var in foreach context
            unset($decorator);
        }

        return $html_form;
    }

    /**
     * Returns a XHTML string representation of the form.
     *
     * @see Koch_Form::render()
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    /**
     * ===================================================================================
     *      Formelement Handling (add, del, getByPos, getByName)
     * ===================================================================================.
     */

    /**
     * PSR-0 is idiotic! Inventors should shit theirs pants and attend PHP conferences wearing them.
     *
     * This is a case-insensitive file exists check.
     * This allows checking for names/file, which are not only ucfirst(), e.g. "SubmitButton".
     *
     * @param string $fileName
     *
     * @return string
     */
    public static function fileExists($fileName)
    {
        if (is_file($fileName)) {
            return $fileName;
        }

        // handle case insensitive requests
        $directoryName = dirname($fileName);
        $fileArray     = glob($directoryName . '/*', GLOB_NOSORT);
        foreach ($fileArray as $file) {
            if (false !== stripos($file, $fileName . '.')) {
                return realpath($file);
            }
        }

        return false;
    }

    /**
     * Adds a formelement to the form.
     *
     * You don't know the formelements available? Then take a look at
     * a) the directory core\viewhelper\form\formelements\*
     * b) the manual
     *
     * @param $formelement string|object Name of formelement or Object implementing the Koch_Form_Interface
     * @param $attributes array Attributes for the formelement.
     * @param $position int The position of this formelement in the formelements stack.
     *
     * @return object \Koch\Form\Form
     */
    public function addElement($formelement, $attributes = null, $position = null)
    {
        /**
         * Continue, if parameter $formelement is an formelement object, implementing
         * the \Koch\Form\Element\Interface. Else it's a string with the name of the formelement,
         * which we pass to the factory to deliver that formelement object.
         *
         * Note: Checking for the interface is necessary here, because checking for type string,
         * like if(is_string(formelement)), would result in true, because all formelement
         * objects provide the __toString() method for easier rendering.
         */
        if (($formelement instanceof \Koch\Form\FormelementInterface) === false) {
            $formelement = self::formelementFactory($formelement);
        }

        // for easier use of the formelement "file":
        // this switches the "encytype" attribute of the form tag.
        if ($formelement instanceof \Koch\Form\Formelement\File) {
            $this->setEncoding('multipart/form-data');
        }

        // helper for setting formelement attributes directly when adding
        if (is_array($attributes)) {
            $formelement->setAttributes($attributes);
        }

        /*
         * create formelement identifier automatically if not set manually.
         * this is needed for javascript selections via id tag.
         */
        if (strlen((string) $formelement->getID()) === 0) {
            $formelement->setID($formelement->type . '-formelement-' . count($this->formelements));
        }

        // if we don't have a position to order the elements, we just add an element
        // this is the default behaviour
        if ($position === null) {
            $this->formelements[] = $formelement;
        } elseif (is_int($position)) {
            // else we position the element under it's number to keep things in an order

            // hmpf, there is already an element at this position
            if ($this->formelements[$position] !== null) {
                // insert the new element to the requested position and reorder
                $this->formelements = $this->arrayInsert($formelement, $position, $this->formelements);

                // after repositioning we need to recalculate the formelement ids
                $this->regenerateFormelementIdentifiers();
            }
        } else {
            // just add to the requested position
            $this->formelements[$position] = $formelement;
        }

        // return formelement object -> fluent interface / method chaining
        return $formelement;
    }

    /**
     * Regenerates the generic identifier of each formelement in the stack by it's position.
     * The formelement at stack position 1 becomes "name-formelement-1", etc.
     */
    public function regenerateFormelementIdentifiers()
    {
        $pos_lastpart = '';
        $pos          = '';
        $firstpart    = '';
        $id           = '';

        $i = 0;

        foreach ($this->formelements as $formelement) {
            $id = $formelement->getID();

            /*
             * the autogenerated id string has the following abstract format:
             * "type-formelement-id". it's exact string length is unknown.
             * the last part separated by a minus (the id part) is stripped off
             * of the string.
             */
            $pos_lastpart = strrpos((string) $id, '-') + 1;
            $pos          = strlen((string) $id) - $pos_lastpart;
            $firstpart    = substr((string) $id, 0, -$pos);

            // the new id is then appended to the remaining firstpart of the string
            $id = $firstpart .= $i;

            $formelement->setID($id);

            ++$i;
        }

        unset($i, $pos_lastpart, $pos, $firstpart, $id);
    }

    /**
     * Inserts value at a certain index into an array.
     *
     * @param array $array The "old" array.
     * @param int   $index The index to insert the value
     * @return array $array with $value at position $index.
     */
    private function arrayInsert(mixed $value, $index, &$array)
    {
        return array_merge(array_slice($array, 0, $index), [$value], array_slice($array, $index));
    }

    /**
     * Removes a formelement by name (not type!).
     *
     * @param string $name
     *
     * @return bool
     */
    public function delElementByName($name)
    {
        $cnt_formelements = count($this->formelements);
        for ($i = 0; $i < $cnt_formelements; ++$i) {
            if ($name === $this->formelements[$i]->getName()) {
                unset($this->formelements[$i]);

                return true;
            }
        }

        return false;
    }

    /**
     * Fetches a formelement via it's position number.
     *
     * @param $position int The position number the requested formelement (ordering).
     *
     * @return Koch_Formelement $formelement Object implementing the Koch_Form_Interface
     */
    public function getElementByPosition($position)
    {
        if (is_numeric($position) and isset($this->formelements[$position])) {
            return $this->formelements[$position];
        }

        return;
    }

    /**
     * Fetches a formelement via it's name (not type!).
     *
     * @param $name string The name of the requested formelement.
     *
     * @return Koch_Formelement $formelement Object
     */
    public function getElementByName($name)
    {
        foreach ($this->formelements as $formelement) {
            if ($name === $formelement->getName()) {
                return $formelement;
            }
        }

        return;
    }

    /**
     * Fetches a formelement by it's name or position or
     * returns the last element in the stack as default.
     *
     * @param $position string|int Name or position of the formelement.
     *
     * @return Koch_Formelement $formelement Object
     */
    public function getElement($position = null)
    {
        $formelement_object = '';

        // if no position is incomming, the last formelement is returned.
        // this is the normal call to this method, while chaining.
        if ($position === null) {
            // fetch last item of array = last_formelement
            $formelement_object = end($this->formelements);
        } elseif (is_numeric($position)) {
            // fetch formelements from certain position
            $formelement_object = $this->getElementByPosition($position);
        } else {
            // position is_string
            $formelement_object = $this->getElementByName($position);
        }

        /* @var \Koch\Form\FormElement */

        return $formelement_object;
    }

    /**
     * ===================================================================================
     *      Formelement Factory
     * ===================================================================================.
     */

    /**
     * Factory method. Instantiates and returns a new formelement object.
     * For a list of all available formelements visit the "/formelements" directory.
     *
     * @return Koch_Formelement object
     */
    public static function formelementFactory($formelement)
    {
        // case-insensitve file in folder check to get filename, which is the classname
        // thanks to PSR-0
        $file = self::fileExists(__DIR__ . '/Elements/' . $formelement);

        if ($file === false) {
            throw new \Exception('The Formelement "' . $formelement . '" does not exist.');
        }

        // get PSR-0 classname from file
        $pi        = pathinfo($file);
        $classname = $pi['filename'];

        // class = namespace "Koch\Form\Element\" + formelement name
        $class = '\Koch\Form\Elements\\' . $classname;

        // instantiate the new formelement and return
        return new $class();
    }

    /**
     * ===================================================================================
     *      Form Processing
     * ===================================================================================.
     */

    /**
     * processForm.
     *
     * This is the main formular processing loop.
     * If the form does not validate, then redisplay it,
     * else present "Success"-Message!
     */
    public function processForm()
    {
        // check, if form has been submitted properly
        if ($this->validateForm()) {
            /*
             * Success - form content valid.
             * The "noerror" decorator implementation decides,
             * if a success web page or a flashmessage is used.
             */
            $this->addDecorator('NoError');
        } else {
            /*
             * Failure - form was not filled properly.
             * Redisplay the form with error decorator added.
             */
            $this->addDecorator('Errors');
        }
    }

    /**
     * Get the data array.
     *
     * @return array containing all the form data.
     */
    protected function bind()
    {
    }

    /**
     * Set Values to Form.
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
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (null === $data) { // fetch data from POST
            if ('POST' === \Koch\Http\HttpRequest::getRequestMethod()) {
                $data = (new \Koch\Http\HttpRequest())->getPost();
            }
        }

        // now we got an $data array to populate all the formelements with (setValue)
        foreach ($data as $key => $value) {
            foreach ($this->formelements as $formelement) {

                /*
                 * Exclude some formelements from setValue() by type, e.g. Buttons, etc.
                 * Setting the value would just change the visible "name" of these elements.
                 */
                $type = $formelement->getType();
                if (true === in_array($type, ['submit', 'button', 'cancelbutton', 'resetbutton'], true)) {
                    continue;
                }

                // data[key] and formelement[name] have to match
                //if ($formelement->getName() == ucfirst($key)) {
                    $formelement->setValue($value);
                //}
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
        $values = [];

        foreach ($this->formelements as $formelement) {
            /*
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
     * ===================================================================================.
     */

    /**
     * Is a shortcut/proxy/convenience method for addDecorator()
     * <strong>WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM</strong>.
     *
     * @see $this->addDecorator()
     *
     * @param string $decorators Array of decorator objects or names or just one string.
     * @param array  $attributes Array of properties for the decorator object.
     *
     * @return Koch_Formdecorator object
     */
    public function setDecorator($decorators, $attributes = null)
    {
        return $this->addDecorator($decorators, $attributes);
    }

    /**
     * Add multiple decorators at once.
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
     * <strong>WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM</strong>.
     *
     * @example
     * $form->addDecorator('fieldset')->setLegend('legendname');
     *
     * @param array $decorator  Array of decorator objects or names or just one string.
     * @param array $attributes Array of properties for the decorator object.
     *
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
        if (false === in_array($decorator, $this->formdecorators, true)) {
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
     * Getter Method for the formdecorators.
     *
     * @return array with registered formdecorators
     */
    public function getDecorators()
    {
        return $this->formdecorators;
    }

    /**
     * Toggles the Usage of Default Form Decorators
     * If set to false, registerDefaultFormDecorators() is not called during render().
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
     * Set default form decorators (form).
     */
    public function registerDefaultFormDecorators()
    {
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
        if (isset($this->formdecorators[$decorator]) || array_key_exists($decorator, $this->formdecorators)) {
            unset($this->formdecorators[$decorator]);
        }
    }

    public function getDecorator($decorator)
    {
        if (isset($this->formdecorators[$decorator])) {
            return $this->formdecorators[$decorator];
        } else {
            throw new \InvalidArgumentException('The Form does not have a Decorator called "' . $decorator . '".');
        }
    }

    /**
     * Factory method. Instantiates and returns a new formdecorator object.
     *
     * @param string Name of Formdecorator.
     * @param string $decorator
     *
     * @return Koch_Formdecorator
     */
    public function decoratorFactory($decorator)
    {
        $classmap = [
            'html5validation' => 'Html5Validation',
            'noerror'         => 'NoError',
        ];

        if (isset($classmap[$decorator]) || array_key_exists($decorator, $classmap)) {
            $decorator = $classmap[$decorator];
        } else {
            $decorator = ucfirst($decorator);
        }

        $class = 'Koch\Form\Decorators\Form\\' . ucfirst($decorator);

        return new $class();
    }

    /**
     * Sets the Decorator Attributes Array.
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
     * Returns the Decorator Attributes Array.
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
     * Array Structure.
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
     * ===================================================================================.
     */

    /**
     * setFormelementDecorator.
     *
     * Is a shortcut/proxy/convenience method for addFormelementDecorator()
     *
     * @see $this->addFormelementDecorator()
     *
     * WATCH OUT! THIS BREAKS THE CHAINING IN REGARD TO THE FORM
     *
     * @param string $decorator
     *
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
     *
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
     * @param string            $decorator                The formelement decorator(s) to apply to the formelement.
     * @param int|string|object $formelement_pos_name_obj Position in the formelement stack or Name of formelement.
     *
     * @return object \Koch\Form\Decorators\Formelement\Interface
     */
    public function addFormelementDecorator($decorator, $formelement_pos_name_obj = null)
    {
        if (true === empty($this->formelements)) {
            throw new \RuntimeException('No Formelements found. Add the formelement(s) first, then decorate!');
        }

        $formelement_object = '';

        if (false === is_object($formelement_pos_name_obj)) {
            $formelement_object = $this->getElement($formelement_pos_name_obj);
        }

        return $formelement_object->addDecorator($decorator);
    }

    /**
     * Removes a decorator from a formelement.
     *
     * @param string $decorator
     * @param type   $formelement_position
     */
    public function removeFormelementDecorator($decorator, $formelement_position = null)
    {
        $formelement = $this->getElement($formelement_position);
        $formelement->removeDecorator($decorator);
    }

    /**
     * ===================================================================================
     *      Form Validation
     * ===================================================================================.
     */

    /**
     * Adds a validator to the formelement.
     *
     * @return Form
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
     * If a formelement is not valid, the error flag on the form is raised and the error message
     * of the formelement is transferred to the error message stack of the form.
     *
     * @return bool Returns true if form validates, false if validation fails, because errors exist.
     */
    public function validateForm()
    {
        foreach ($this->formelements as $formelement) {
            if ($formelement->validate() === false) {
                $this->hasErrors(true);
                $this->addErrorMessages($formelement->getErrorMessages());
            }
        }

        // if form has errors, it does not validate
        return $this->hasErrors() ? false : true;
    }

    /**
     * ===================================================================================
     *      Form Errormessages
     * ===================================================================================.
     */

    /**
     * Returns the error state of the form.
     *
     * @param bool $boolean
     *
     * @return bool True, if form has an error. False, otherwise.
     */
    public function hasErrors($boolean = null)
    {
        if (is_bool($boolean)) {
            $this->error = $boolean;
        }

        return $this->error;
    }

    /**
     * @param string $errorMessage
     */
    public function addErrorMessage($errorMessage)
    {
        $this->errorMessages[] = $errorMessage;
    }

    public function addErrorMessages(array $errorMessages)
    {
        $this->errorMessages = $errorMessages;
    }

    public function resetErrorMessages()
    {
        $this->errorMessages = [];
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * ============================
     *    Magic Methods: get/set
     * ============================.
     */

    /**
     * Magic Method: set.
     *
     * @param $name Name of the attribute to set to the form.
     * @param $value The value of the attribute.
     */
    public function __set($name, $value)
    {
        $this->setAttributes([$name => $value]);
    }

    /**
     * Magic Method: get.
     *
     * @param $name
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
}