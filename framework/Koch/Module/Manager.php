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

namespace Koch\Module;

/**
 * Module Manager.
 *
 * Handles
 * - enable,
 * - disable,
 * - install,
 * - uninstall and
 * - update of a module.
 *
 * You might select the module with selectModule() or via constructor injection.
 */
class Manager
{
    public function __construct($module)
    {
        // load the relavant stuff of the module
        // $this->config = loadRelevantStuff($module);

        // allow fluent chaining
        return $this;
    }

    /**
     * Enables a module.
     *
     * @return bool True, if module was enabled. False otherwise.
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
     * Disables a module.
     *
     * @return bool True, if module was enabled. False otherwise.
     */
    public function disable()
    {
        return false;
    }

    /**
     * Install module.
     *
     * @return bool True, if module was installed. False otherwise.
     */
    public function install()
    {
        return false;
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall()
    {
        return false;
    }

    /**
     * Update module.
     *
     * @return bool
     */
    public function update()
    {
        return false;
    }
}
