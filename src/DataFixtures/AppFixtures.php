<?php

namespace App\DataFixtures;

use App\Entity\Builds;
use App\Entity\Tutorial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for builds : [author]
     * @return \\Generator
     */
    private static function buildsDataGenerator()
    {
        yield ["user1"];
        yield ["user2"];
        yield ["user3"];
    }
    
    /**
     * Generates initialization data for tutorials of builds: [name, author]
     * @return \\Generator
     */
    private static function buildsTutorialsGenerator()
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
        
        // Loading of test Builds
        foreach (self::buildsDataGenerator() as [$author] ) {
            $builds = new Builds();
            $builds->setAuthor($author);
            $manager->persist($builds);
        }
        $manager->flush();
        
        
        // Loading of test Tutorials
        $buildsRepo = $manager->getRepository(Builds::class);
        
        foreach (self::buildsTutorialsGenerator() as [$name, $author])
        {
            $builds = $buildsRepo->findOneBy(['author' => $author]);
            
            $tutorial = new Tutorial();
            $tutorial->setAuthor($author);
            $tutorial->setName($name);
            
            $builds->addTutorial($tutorial);
            
            // there's a cascade persist on film-recommendations which avoids persisting down the relation
            $manager->persist($builds);
        }
        $manager->flush();
    }
}
