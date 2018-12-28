<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPersonData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $person = new Person();
        $person->setFirstName('Tom');
        $person->setLastName('Hanks');
        $person->setDateOfBirth(new \DateTime('1957-12-10'));
        $manager->persist($person);

        $manager->flush();
    }
}
