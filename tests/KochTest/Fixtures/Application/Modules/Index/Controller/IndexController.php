<?php
/**
 * This explains your WTF!? when seeing the namespace...
 *
 * Application Namespace = KochTest\Fixtures\APP\
 * Module Namespace      = \Modules
 * Module                = \Index
 * Controller            = \Controller\IndexController
 */
namespace KochTest\Fixtures\APP\Modules\Index\Controller;

use Koch\Module\ModuleInterface;

class IndexController //implements ModuleInterface
{
    public function actionList()
    {

    }

    public function carefullyHandcrafted()
    {
        return false;
    }
}
