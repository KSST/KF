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
 * Interface for all modules which implement a specific action structure.
 * Inspired by Sinatra.
 *
 * Force classes implementing the interface to define these (must have) methods!
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Module
 */
interface ModuleInterface
{
    public function action_list();     // GET     /foos
    public function action_show();     // GET     /foos/:foo_id
    public function action_new();      // GET     /foos/new
    public function action_edit();     // GET     /foos/:foo_id/edit
    public function action_create();   // POST    /foos
    public function action_update();   // PUT     /foos/:foo_id
    public function action_destroy();  // DELETE  /foos/:foo_id
}
