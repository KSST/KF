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
    public function actionList();     // GET     /foos
    public function actionShow();     // GET     /foos/:foo_id
    public function actionNew();      // GET     /foos/new
    public function actionEdit();     // GET     /foos/:foo_id/edit
    public function actionCreate();   // POST    /foos
    public function actionUpdate();   // PUT     /foos/:foo_id
    public function actionDelete();   // DELETE  /foos/:foo_id
}
