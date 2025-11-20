<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Library;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager) : void
    {
        $this->loadMembers($manager);
    }

    private function loadMembers(ObjectManager $manager)
    {
        foreach ($this->getMemberData() as [$email, $name, $plainPassword,$role]) {
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $plainPassword);
            $member->setEmail($email);
            $member->setPassword($password);
            $member->setName($name);
            $library = new Library();
            $member->setLibrary($library);
            
            $roles = array();
            $roles[] = $role;
            $member->setRoles($roles);

            $manager->persist($member);
        }
        $manager->flush();
    }
    private function getMemberData()
    {
        yield [
            'admin@localhost',
            'admin',
            'admin123',
            'ROLE_ADMIN'
        ];
        yield [
            'spokez@localhost',
            'spokez',
            'spokez123',
            'ROLE_Member'
        ];
        yield [
            'lyanou@localhost',
            'lyanou',
            'lyanou123',
            'ROLE_Member'
        ];
    }
}
