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

namespace Koch\Module;

/**
 * Module Manager
 *
 * Handles
 * - enable,
 * - disable,
 * - install,
 * - uninstall and
 * - update of a module.
 *
 * You might select the module with selectModule() or via constructor injection.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Module
 */
class Manager
{
    public function __construct($module)
    {
        // load the relavant stuff of the module
        // $this->config = loadRelevantStuff($module);

        // allow fluent chaining
        return this;
    }

    /**
     * Enables a module
     *
     * @return boolean True, if module was enabled. False otherwise.
     */
    public function enable()
    {
        // a) get config
        // b) change disabled to enabled
        // c) invalidate global module autoload cache?
        // d) re-charge the cache with the new value?
        return false;
    }

    /**
     * Disables a module
     *
     * @return boolean True, if module was enabled. False otherwise.
     */
    public function disable()
    {
        return false;
    }

    /**
     *
     * @return boolean True, if module was installed. False otherwise.
     */
    public function install()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function uninstall()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function update()
    {
        return false;
    }
}
