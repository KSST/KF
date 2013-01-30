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
 * Class for loading of Events from Config files.
 *
 * Purpose:
 * Eventloader handles the loading and registering of events by using event configuration files.
 */
class Loader
{
    /**
     * Loads and registers all events of the core and all activated modules.
     */
    public static function loadEvents()
    {
        //self::loadAllModuleEvents();
        self::loadCoreEvents();
    }

    /**
     * Registers multiple Events by Name
     *
     * @param array $events_array  eventname => filename
     * @param array $event_objects eventname => object
     */
    public static function loadEventHandlers($events)
    {
        if (empty($events) or is_array($events) === false) {
            return;
        } else { // ok, we got an array with some event names
            foreach ($events as $event) {
                // array[0] filename
                $filename = $array[0];

                // array[1] classname
                $classname = \Koch\Functions\Functions::ensurePrefixedWith($array[1], 'Koch_Event_');

                // load eventhandler
                Koch_Loader::requireFile($filename, $classname);

                // instantiate eventhandler
                $event_object = new $classname();

                // add the eventhandler to the dispatcher
                $eventdispatcher = Koch_Eventdispatcher::instantiate();
                $eventdispatcher->addEventHandler($event, $event_object);
            }
        }
    }

    /**
     * Loads and registers the core eventhandlers according to the event configuration file.
     * The event configuration for the core is file is /configuration/events.config.php.
     */
    public static function loadCoreEvents()
    {
        $events = include APPLICATION_PATH . 'Configuration/events.php';

        self::loadEventHandlers($events);
    }

    /**
     * Loads and registers the eventhandlers for a module according to the module event configuration file.
     * The event configuration files for a module resides in /module/module.events.php (abstract).
     * For instance the eventcfg filename for module news is /modules/news/news.events.php.
     *
     * @param string $modulename Name of a module.
     */
    public static function loadModuleEvents($modulename)
    {
        $events = include APPLICATION_MODULES_PATH . $modulename . '/'. $modulename . '.events.php';

        self::loadEventHandlers($events);
    }

    /**
     * Loads and registers the eventhandlers for all activated modules.
     */
    public static function loadAllModuleEvents()
    {
        // fetch all activated modules
        $modules = Koch_ModuleInfoController::getAllActivatedModules();

        // load eventhandlers for each module
        foreach ($modules as $module) {
            self::loadModuleEvents($module);
        }
    }
}
