<?php
/**
 * This explains your WTF!? when seeing the namespace...
 *
 * Application Namespace = KochTest\Fixtures\APP\
 * Module Namespace      = \Modules
 * Module                = \Index
 * Controller            = \Controller\IndexController
 */
namespace KochTest\Fixtures\APP\Modules\User\Controller;

use Koch\Module\ModuleInterface;

class AccountController //implements ModuleInterface
{
    public function actionLogin()
    {

    }

    public function carefullyHandcrafted()
    {
        return false;
    }
}
