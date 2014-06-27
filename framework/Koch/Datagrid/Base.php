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

namespace Koch\Datagrid;

/**
 * Datagrid Base
 *
 * It is the parent class for the datagrid
 * and provides common methods.
 */
class Base
{
    /**
     * The data to render in the grid.
     *
     * @var array
     */
    public $data;

    // scoped vars
    private $alias;
    private $id;
    private $name;
    private $class;
    private $style;

    /**
     * Label for the datagrid
     *
     * @var string
     */
    private $label = 'Label';

    /**
     * The caption (<caption>...</caption>) for the datagrid
     *
     * @var string
     */
    private $caption = 'Caption';

    /**
     * The description for the datagrid
     *
     * @var string
     */
    private $description = 'This is a Datagrid.';

    /**
     * Base URL for the Datatable
     *
     * @var string
     */
    private static $baseURL = null;

    /**
     *  Setter Methods for a Datagrid
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $alias = str_replace('\\', '_', $alias);
        $this->alias = $alias;

        return $this;
    }

    /**
     * Sets the BaseURL
     *
     * @param string $baseURL The baseURL for the datatable
     */
    public function setBaseURL($baseURL = null)
    {
        if (self::$baseURL === null) {
            self::$baseURL = Koch\Http\HttpRequest::getRequestURI();
        } else {
            self::$baseURL = $baseURL;
        }

        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set datagrid state from options array
     *
     * @param  array    $options
     * @return Datagrid
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                // setter method exists
                $this->$method($value);
            } else {
                throw new Koch\Exception\Exception('Unknown property ' . $key . ' for Datagrid');
            }
        }

        return $this;
    }

    /**
     * Getter Methods for Datagrid
     */

    public function getAlias()
    {
        return $this->alias;
    }

    public static function getBaseURL()
    {
        return self::$baseURL;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add an url-string to the baseurl
     *
     * @example
     *   $sUrl = $this->appendUrl('dg_Sort=0:ASC');
     *
     * @param string $appendString String to append to the URL.
     */
    public static function appendUrl($appendString)
    {
        $separator = '?';

        if (preg_match('#\?#', self::getBaseURL())) {
            $separator = '&amp;';
        }

        $cleanAppendString = preg_replace('#^&amp;#', '', $appendString);
        $cleanAppendString = preg_replace('#^&#', '', $cleanAppendString);
        $cleanAppendString = preg_replace('#^\?#', '', $cleanAppendString);
        $cleanAppendString = preg_replace('#&(?!amp;)#i', '&amp;', $cleanAppendString);

        return self::getBaseURL() . $separator . $cleanAppendString;
    }
}
