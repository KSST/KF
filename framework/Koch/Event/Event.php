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

namespace Koch\Event;

/**
 * Class represents an Event.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Event
 */
class Event implements \ArrayAccess
{
    /**
     * @var Name of the event
     */
    private $eventname;

    /**
     * @var array The context of the event triggering. Often the object from where we are calling.
     */
    private $context;

    /**
     * @var string Some pieces of additional information
     */
    private $info;

    /**
     * @var boolean The cancel state of the event
     */
    private $cancelled = false;

    /**
     * Event constructor
     *
     * @param $name     Event Name
     * @param $context  The context of the event triggering. Often the object from where we are calling. Default null.
     * @param $info     Some pieces of additional information. Default null.
     */
    public function __construct($name, $context = null, $info = null)
    {
        $this->eventname = $name;
        $this->context = $context;
        $this->info = $info;
    }

    /**
     * getName returns the Name of the Event.
     *
     * @return string
     */
    public function getName()
    {
        return $this->eventname;
    }

    /**
     * getContext returns the Context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * getInfo returns
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * getCancelled returns the cancelled-status of the event
     *
     * @returns boolean
     */
    public function getCancelled()
    {
        return (boolean) $this->cancelled;
    }

    /**
     * sets the cancelled flag to true
     */
    public function cancel()
    {
        $this->cancelled = true;
    }

    /**
     * ArrayAccess Implementation
     */

    /**
     * Returns true if the parameter exists (implements the ArrayAccess interface).
     *
     * @param string $name The parameter name
     *
     * @return Boolean true if the parameter exists, false otherwise
     */
    public function offsetExists($name)
    {
        return isset($this->context[$name]) || array_key_exists($name, $this->context);
    }

    /**
     * Returns a parameter value (implements the ArrayAccess interface).
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     */
    public function offsetGet($name)
    {
        if (isset($this->context[$name]) === true || true === array_key_exists($name, $this->context)) {
            return $this->context[$name];
        } else {
            throw new \Koch\Exception\Exception(
                sprintf(_('The event "%s" has no context parameter "%s" .'), $this->eventname, $name)
            );
        }
    }

    /**
     * Sets a parameter (implements the ArrayAccess interface).
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     */
    public function offsetSet($name, $value)
    {
        $this->context[$name] = $value;
    }

    /**
     * Removes a parameter (implements the ArrayAccess interface).
     *
     * @param string $name The parameter name
     */
    public function offsetUnset($name)
    {
        unset($this->context[$name]);
    }
}
