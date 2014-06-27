<?php
namespace KochTest\Permissions;

use Koch\Permissions\Acl;

class ACL extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Acl
     */
    protected $acl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->acl = new Acl;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->acl);
    }

    /**
     * @covers Koch\Permissions\Acl::addRole
     */
    public function testAddRole()
    {
        $this->acl->addRole('Hausbewohner');
        $this->acl->addRole('Hausbewohner', 'Mieter1');
        $this->acl->addRole('Hausbewohner', 'Mieter2');
        $this->acl->addRole('Hausverwalter');
    }

    /**
     * @covers Koch\Permissions\Acl::addResource
     */
    public function testAddResource()
    {
        $this->acl->addResource('Haus');
        $this->acl->addResource('Haus', 'Wohnung1');
        $this->acl->addResource('Haus', 'Wohnung2');
    }

    /**
     * @covers Koch\Permissions\Acl::addRuleAllow
     */
    public function testRuleAllow()
    {
        $this->acl->ruleAllow('Hausverwalter', 'view', 'Haus');
        $this->acl->ruleAllow('Mieter1', 'view', 'Wohnung1');
        $this->acl->ruleAllow('Mieter2', 'view', 'Wohnung2');

    }

    /**
     * @covers Koch\Permissions\Acl::addRuleDeny
     */
    public function testRuleDeny()
    {
        $this->acl->ruleDeny("Mieter1", "view", "Wohnung2");
        $this->acl->ruleDeny("Mieter2", "view", "Wohnung1");
    }

    /**
     * @covers Koch\Permissions\Acl::isAllowed
     */
    public function testIsAllowed()
    {
        // the shorthand in the user object is $user->isAllowed($action, $resource);
        // the Role is incomming via the user object (user_id -> roles table)
        // action and resource are identified by the router and exist in the TargetRoute object
        $this->acl->isAllowed($role, $action, $resource);
    }

}
