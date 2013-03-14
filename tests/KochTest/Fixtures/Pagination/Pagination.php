<?php

namespace MyDataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use KochTest\Fixtures\Doctrine\Entity\User;

class LoadPaginationData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // user 1
        $user = new User;
        $user->setId(1);
        $user->setUsername('jakoch');
        $user->setEmail('jakoch@web.de');
        $user->setPassword('test');

        $manager->persist($user);
        $manager->flush();

        // user 2
        $user = new User;
        $user->setId(2);
        $user->setUsername('someUsername');
        $user->setEmail('some@email.com');
        $user->setPassword('somePassword');

        $manager->persist($user);
        $manager->flush();
    }
}
