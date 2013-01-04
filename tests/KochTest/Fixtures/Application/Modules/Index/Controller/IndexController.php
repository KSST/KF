<?php
/**
 * This explains your WTF!? when seeing the namespace...
 *
 * Application Namespace = KochTest\Fixtures\Application\
 * Module Namespace      = \Modules
 * Module                = \Index
 * Controller            = \Controller\IndexController
 */
namespace KochTest\Fixtures\Application\Modules\Index\Controller;

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
