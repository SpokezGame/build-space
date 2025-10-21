<?php

namespace App\DataFixtures;

use App\Entity\TutorialLibrary;
use App\Entity\Tutorial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for TutorialLibraries : [author]
     * @return \\Generator
     */
    private static function tutorialLibraryGenerator()
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
        foreach (self::tutorialLibraryGenerator() as [$author] ) {
            $tutorialLibrary = new TutorialLibrary();
            $tutorialLibrary->setAuthor($author);
            $manager->persist($tutorialLibrary);
        }
        $manager->flush();
        
        
        // Loading of test ListTutorials
        $tutorialLibraryRepo = $manager->getRepository(TutorialLibrary::class);
        
        foreach (self::tutorialGenerator() as [$name, $author])
        {
            $tutorialLibrary = $tutorialLibraryRepo->findOneBy(['author' => $author]);
            
            $tutorial = new Tutorial();
            $tutorial->setAuthor($author);
            $tutorial->setName($name);
            
            $tutorialLibrary->addTutorial($tutorial);
            // there's a cascade persist on tutorialLibrary
            $manager->persist($tutorialLibrary);
        }
        $manager->flush();
    }
}
