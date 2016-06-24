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

class CrudController extends Controller implements ModuleInterface
{
    public function actionList()
    {
        $collection = $this->getCollection();
        $template   = $this->getListTemplate();

        return $this->render($template, $collection);
    }

    public function actionShow($id)
    {
        $model    = $this->getModel()->find($id);
        $template = $this->getViewTemplate();

        return $this->render($template, ['model' => $model]);
    }

    public function actionNew()
    {
        $this->actionEdit();
    }

    public function actionDelete($id, $confirm = false)
    {
        $model = $this->getModel()->find($id);

        if (false === $confirm) {
            return $this->render($this->getDeleteConfirmTemplate(), ['model' => $model]);
        }

        $model->delete();

        return $this->render($this->getDeleteSuccessTemplate(), ['model' => $model]);
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
        $form    = $this->getForm();

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

        return $this->render($this->getEditTemplate(), ['form' => $form]);
    }
}
