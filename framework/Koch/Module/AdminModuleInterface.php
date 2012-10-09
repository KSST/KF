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
interface AdminModuleInterface
{
    public function actionAdminList();     // GET     /foos
    public function actionAdminShow();     // GET     /foos/:foo_id
    public function actionAdminNew();      // GET     /foos/new
    public function actionAdminEdit();     // GET     /foos/:foo_id/edit
    public function actionAdminInsert();   // POST    /foos
    public function actionAdminUpdate();   // PUT     /foos/:foo_id
    public function actionAdminDelete();   // DELETE  /foos/:foo_id
}
