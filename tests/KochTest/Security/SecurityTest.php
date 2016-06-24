<?php

namespace KochTest\Security;

use Koch\Security\Security;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * testMethodgenerate_salt().
     */
    public function testMethodgenerateSalt()
    {
        // generate a salt with length
        $salt = Security::generateSalt(12);

        // ensure $salt is a string
        $this->assertTrue(is_string($salt), true);

        // ensure $salt has correct length
        $this->assertEquals(strlen($salt), 12);
    }

    public function testMethodgenerateHash()
    {
        $hash_md5 = \Koch\Security\Security::generateHash('md5', 'admin');

        $this->assertSame('21232f297a57a5a743894a0e4a801fc3', $hash_md5);

        $hash_sha1 = \Koch\Security\Security::generateHash('sha1', 'admin');

        $this->assertSame('d033e22ae348aeb5660fc2140aec35850c4da997', $hash_sha1);
    }

    public function testMethodbuildSaltedHash()
    {
        $salted_hash = \Koch\Security\Security::buildSaltedHash('admin', 'md5');

        $this->assertTrue(is_array($salted_hash), true);
    }

    public function testMethodcheckSaltedHash()
    {
        // md5('admin'); from form input
        $passwordhash = '21232f297a57a5a743894a0e4a801fc3';
        // expected, from db
        $databasehash = '7ff3adfa18a8ad7f115e90ce2c44a0ec';
        // from db
        $salt           = 'Sko5ie';
        $hash_algorithm = 'md5';

        $bool = \Koch\Security\Security::checkSaltedHash($passwordhash, $databasehash, $salt, $hash_algorithm);

        $this->assertTrue($bool, true);
    }
}
