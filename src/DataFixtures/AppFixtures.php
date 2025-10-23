<?php

namespace App\DataFixtures;

use App\Entity\Library;
use App\Entity\Theme;
use App\Entity\Tutorial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for libraries : [author]
     * @return \\Generator
     */
    private static function libraryGenerator()
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
    
    /**
     * Generates initialization data for themes : [name, [tutorial1, tutorial2, ...]]
     * @return \\Generator
     */
    private static function themeGenerator()
    {
        yield ["Fantasy", ["Fantasy House", "Well", "Chair"]];
        yield ["House", ["Cupboard", "Clock", "Chair"]];
    }
    
    public function load(ObjectManager $manager) : void
    {
        
        // Loading of test Libraries
        foreach (self::libraryGenerator() as [$author] ) {
            $library = new Library();
            $library->setAuthor($author);
            $manager->persist($library);
        }
        $manager->flush();
        
        
        // Loading of test Tutorials
        $libraryRepo = $manager->getRepository(Library::class);
        
        foreach (self::tutorialGenerator() as [$name, $author])
        {
            $library = $libraryRepo->findOneBy(['author' => $author]);
            
            $tutorial = new Tutorial();
            $tutorial->setAuthor($author);
            $tutorial->setName($name);
            
            $library->addTutorial($tutorial);
            // there's a cascade persist on library
            $manager->persist($library);
        }
        $manager->flush();
        
        // Loading of Themes
        $tutorialRepo = $manager->getRepository(Tutorial::class);
        
        foreach (self::themeGenerator() as [$name, $tutorials])
        {
            $theme = new Theme();
            $theme->setName($name);
            
            foreach ($tutorials as $tutorial_name)
            {
                $tutorial = $tutorialRepo->findOneBy(['name' => $tutorial_name]);
                
                $tutorial->addTheme($theme);
                
                $manager->persist($tutorial);
            }
        }
        $manager->flush();
    }
}
