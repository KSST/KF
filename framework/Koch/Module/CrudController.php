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
 *
 */

namespace Koch\Module;

class CrudController extends Controller implements ModuleInterface
{
    public function actionList()
    {
        $collection = $this->getCollection();
        $template = $this->getListTemplate();

        return $this->render($template, $collection);
    }

    public function actionShow($id)
    {
        $model = $this->getModel()->find($id);
        $template = $this->getViewTemplate();

        return $this->render($template, array('model' => $model));
    }

    public function actionNew()
    {
        $this->actionEdit();
    }

    public function actionDelete($id, $confirm = false)
    {
        $model = $this->getModel()->find($id);

        if (false === $confirm) {
            return $this->render($this->getDeleteConfirmTemplate(), array('model' => $model));
        }

        $model->delete();

        return $this->render($this->getDeleteSuccessTemplate(), array('model' => $model));
    }

    public function actionCreate($id)
    {
        $this->actionEdit($id);
    }

    public function actionInsert($id)
    {
        $this->actionEdit($id);
    }

    public function actionUpdate($id)
    {
        $this->actionEdit($id);
    }

    public function actionEdit($id = false)
    {
        $request = $this->getRequest();
        $form = $this->getForm();

        // model bleibt leer, wenn nichts geladen werden kann
        $model = $this->getModel()->find($id);

        if ($request->isPost()) {
            if ($form->isValid($request)) {
                $model->setData($form->getData());
                $model->save();
                $this->setFlashMessage('Speichern ok');

                return $this->redirect($this->getListUrl());
            }
        }

        return $this->render($this->getEditTemplate(), array('form' => $form));
    }
}
