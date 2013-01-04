<?php
/**
 * This explains your WTF!? when seeing the namespace...
 *
 * Application Namespace = KochTest\Fixtures\Application\
 * Module Namespace      = \Modules
 * Module                = \Index
 * Controller            = \Controller\IndexController
 */
namespace KochTest\Fixtures\Application\Modules\User\Controller;

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
