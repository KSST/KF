<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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

namespace Koch\Module;

/**
 * Interface for all modules which implement a specific action structure.
 * Inspired by Sinatra.
 *
 * Force classes implementing the interface to define these (must have) methods!
 */
interface ModuleInterface
{
    public function actionList();     // GET     /foos
    public function actionShow();     // GET     /foos/:foo_id

    /**
     * @return void
     */
    public function actionNew();      // GET     /foos/new
    public function actionEdit();     // GET     /foos/:foo_id/edit

    /**
     * @return void
     */
    public function actionInsert();   // POST    /foos

    /**
     * @return void
     */
    public function actionUpdate();   // PUT     /foos/:foo_id
    public function actionDelete();   // DELETE  /foos/:foo_id
}
