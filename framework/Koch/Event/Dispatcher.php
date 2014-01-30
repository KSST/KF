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
 * Class for registering and triggering eventhandlers.
 *
 * Eventdispatcher is a container class for all the EventHandlers.
 * This class is a helper for event-driven development.
 * You can register eventhandlers under an eventname.
 * When you trigger an event, it performs an lookup of the eventname
 * over all registered eventhandlers and fires the event, if found.
 * This is a very flexible form of communication between objects.
 *
 * @pattern EventDispatcher, Event, Advanced Subject-Observer-Pattern, Notification Queue
 */
class Dispatcher
{
    /**
     * @var object Instance of \Koch\Event\Dispatcher
     */
    private static $instance = null;

    /**
     * @var array All registered Eventhandlers
     */
    private $eventhandlers = array();

    /**
     * Eventdispatcher is a Singleton implementation
     */
    public static function instantiate()
    {
        if (self::$instance === null) {
            self::$instance = new Dispatcher;
        }

        return self::$instance;
    }

    /**
     * Returns an array of all registered eventhandlers for a eventName.
     *
     * @param string $name The event name
     *
     * @return array Array of all eventhandlers for a certain event.
     */
    public function getEventHandlersForEvent($eventName)
    {
        if (isset($this->eventhandlers[$eventName]) === false) {
            return array();
        }

        return $this->eventhandlers[$eventName];
    }

    /**
     * Adds an Event to the Eventhandlers Array
     *
     * Usage
     * <code>
     * function handler1() {
     * echo "A";
     * }
     * function handler2() {
     * echo "B";
     * }
     * function handler3() {
     * echo "C";
     * }
     * $event = \Koch\Event\Dispatcher::instantiate();
     * $event->addEventHandler('event_name1', 'handler1');
     * $event->triggerEvent('event_name1'); // Output: A
     * $event->addEventHandler('event_name2', 'handler2');
     * $event->triggerEvent('event_name2'); // Output: B
     * $event->addEventHandler('event_name1', 'handler3');
     * $event->triggerEvent('event_name1'); // Output: AC
     * </code>
     *
     * @param $eventName    Name of the Event
     * @param $eventobject object|string Instance of \Koch\Event\Event or filename string
     */
    public function addEventHandler($eventName, EventInterface $event_object)
    {
        // if eventhandler is not set already, initialize as array
        if (isset($this->eventhandlers[$eventName]) === false) {
            $this->eventhandlers[$eventName] = array();
        }

        // add event to the eventhandler list
        $this->eventhandlers[$eventName][] = $event_object;
    }

    /**
     * Removes an Event
     *
     * Usage
     * <code>
     * function handler1() {
     * echo "A";
     * }
     * $event = \Koch\Event\Dispatcher::instantiate();
     * $event->addEventHandler('event_name', 'handler1');
     * $event->triggerEvent('event_name'); // Output: A
     * $event->removeEventHandler('event_name', 'handler1');
     * $event->triggerEvent('event_name'); // No Output
     * </code>
     *
     * @param string event name
     * @param mixed event handler
     */
    public function removeEventHandler($eventName, EventInterface $event_object = null)
    {
        // if eventhandler is not added, we have nothing to remove
        if (isset($this->eventhandlers[$eventName]) == false) {
            return false;
        }

        if ($event_object === null) {
            // unset all eventhandlers for this eventName
            unset($this->eventhandlers[$eventName]);
        } else { // unset a specific eventhandler
            foreach ($this->eventhandlers[$eventName] as $key => $registered_event) {
                if ($registered_event == $event_object) {
                    unset($this->$this->eventhandlers[$eventName][$key]);
                }
            }
        }
    }

    /**
     * triggerEvent
     *
     * Usage
     * <code>
     * function handler1() {
     * echo "A";
     * }
     * $event = \Koch\Event\Dispatcher::instantiate();
     * $event->addEventHandler('event_name', 'handler1');
     * $event->triggerEvent('event_name'); // Output: A
     * </code>
     *
     * @param $event Name of Event or Event object to trigger.
     * @param $context default null The context of the event triggering, often the object from where we are calling.
     * @param $info default null Some pieces of information.
     * @return $event object
     */
    public function triggerEvent($event, $context = null, $info = null)
    {
        /**
         * init a new event object with constructor settings
         * if $event is not an instance of \Koch\Event\Event.
         * $event string will be the $name inside $event object,
         * accessible with $event->getName();
         */
        if (false === ($event instanceof Event)) {
            $event = new Event($event, $context, $info);
        }

        // get the Name
        $eventName = $event->getName();

        if (isset($this->eventhandlers[$eventName]) === false) {
            return $event;
        }

        // loop over all eventhandlers and look for that eventname
        foreach ($this->eventhandlers[$eventName] as $eventhandler) {
            // handle the event !!
            $eventhandler->execute($event);

            // break, on cancelled
            if (method_exists($event, 'isCancelled') and $event->isCancelled() == true) {
                break;
            }
        }

        return $event;
    }

    // no construct (singleton)
    protected function __construct()
    {
        return;
    }

    // no clone (singleton)
    private function __clone()
    {
        return;
    }
}
