<?php
/**
 * This explains your WTF!? when seeing the namespace...
 *
 * Application Namespace = KochTest\Fixtures\Application
 * Module Namespace      = \Modules
 * Module                = \Index
 * Controller            = \Controller\IndexController
 */
namespace KochTest\Fixtures\Application\Modules\News\Controller;

use Koch\Module\ModuleInterface;
use Koch\Module\AbstractController;

class NewsController extends AbstractController implements ModuleInterface
{
    public function actionList()
    {

    }

    public function actionShow()
    {

    }

    public function actionNew()
    {

    }

    public function actionEdit()
    {

    }

    public function actionUpdate()
    {

    }

    public function actionInsert()
    {

    }

    public function actionDelete()
    {

    }

    public function carefullyHandcrafted()
    {
        return false;
    }
}
