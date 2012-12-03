<?php
namespace KochTest\ACL;

use Koch\Permissions\ACL;

class ACLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ACL
     */
    protected $acl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->acl = new ACL;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->acl);
    }

    public function testAddRole()
    {
        $this->acl->addRole('Hausbewohner');
        $this->acl->addRole('Hausbewohner', 'Mieter1');
        $this->acl->addRole('Hausbewohner', 'Mieter2');
        $this->acl->addRole('Hausverwalter');
    }

    public function testAddResource()
    {
        $this->acl->addResource("Haus");
        $this->acl->addResource("Haus", "Wohnung1");
        $this->acl->addResource("Haus", "Wohnung2");
    }

    public function testRuleAllow()
    {
        $this->acl->RuleAllow("Hausverwalter", "view", "Haus");
        $this->acl->Ruleallow("Hausbewohner", "view", "course");
        $this->acl->RuleAllow("teacher", "teach", "course");

    }

    public function testRuleDeny()
    {
        $this->acl->RuleDeny("junior-student", "learn", "senior-course");
    }

    public function testIsAllowed()
    {
        // $user->isAllowed($action, $resource);
        // role is incomming via user object (user_id -> roles table)
        $this->acl->isAllowed($role, $action, $resource);
    }

}
