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

namespace Koch\Event;

/**
 * Koch Framework - Class for loading of Events from Config files.
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
     * Registers multiple Events by Name.
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
                $classname = \Koch\Functions\Functions::ensurePrefixedWith($array[1], '\Koch\Event\Event');

                // load eventhandler
                \Koch\Autoload\Loader::requireFile($filename, $classname);

                // instantiate eventhandler
                $event_object = new $classname();

                // add the eventhandler to the dispatcher
                $eventdispatcher = \Koch\Event\Dispatcher::instantiate();
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
        $events = include APPLICATION_MODULES_PATH . $modulename . '/' . $modulename . '.events.php';

        self::loadEventHandlers($events);
    }

    /**
     * Loads and registers the eventhandlers for all activated modules.
     */
    public static function loadAllModuleEvents()
    {
        // fetch all activated modules
        $modules = \Koch\Module\ManifestManager::getAllActivatedModules();

        // load eventhandlers for each module
        foreach ($modules as $module) {
            self::loadModuleEvents($module);
        }
    }
}
