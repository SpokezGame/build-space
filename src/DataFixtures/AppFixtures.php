<?php

namespace App\DataFixtures;

use App\Entity\ListTutorials;
use App\Entity\Tutorial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for list of tutorials : [author]
     * @return \\Generator
     */
    private static function listTutorialsGenerator()
    {
        yield ["user1"];
        yield ["user2"];
        yield ["user3"];
    }
    
    /**
     * Generates initialization data for tutorials : [name, author]
     * @return \\Generator
     */
    private static function tutorialGenerator()
    {
        yield ["Fantasy House", "user1"];
        yield ["Well", "user1"];
        yield ["Cupboard", "user1"];
        yield ["Field", "user2"];
        yield ["Clock", "user2"];
        yield ["Pool table", "user3"];
        yield ["Chair", "user3"];
    }
    
    public function load(ObjectManager $manager) : void
    {
        
        // Loading of test Tutorials
        foreach (self::listTutorialsGenerator() as [$author] ) {
            $listTutorials = new ListTutorials();
            $listTutorials->setAuthor($author);
            $manager->persist($listTutorials);
        }
        $manager->flush();
        
        
        // Loading of test ListTutorials
        $listTutorialsRepo = $manager->getRepository(ListTutorials::class);
        
        foreach (self::tutorialGenerator() as [$name, $author])
        {
            $listTutorials = $listTutorialsRepo->findOneBy(['author' => $author]);
            
            $tutorial = new Tutorial();
            $tutorial->setAuthor($author);
            $tutorial->setName($name);
            
            $listTutorials->addTutorial($tutorial);
            // there's a cascade persist on listTutorials
            $manager->persist($listTutorials);
        }
        $manager->flush();
    }
}
