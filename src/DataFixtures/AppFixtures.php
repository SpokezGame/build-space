<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Member;
use App\Entity\Theme;
use App\Entity\Tutorial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
        ];
    }
    
    /**
     * Generates initialization data for tutorials : [name, author]
     * @return \\Generator
     */
    private static function tutorialGenerator()
    {
        yield ["Fantasy House", "spokez", "A cozy magical dwelling with enchanting details and mystical charm. It's perfect for adventurers seeking comfort and wonder.", "Fantasy House.jpg", 21];
        yield ["Well", "spokez", "A rustic stone well that brings life and realism to any village or medieval courtyard.", "Well.png", 5];
        yield ["Cupboard", "lyanou", "A charming wooden cupboard with fine detailing, a small but elegant touch of homely design.", "Cupboard.jpg", 7];
        yield ["Field", "spokez", "A peaceful stretch of green farmland, ideal for crops, animals, or a tranquil countryside vibe.", "Field.jpg", 0];
        yield ["Clock", "lyanou", "An ornate clock structure showcasing craftsmanship and precision, time stands still in its beauty.", "Clock.jpeg", 0];
        yield ["Pool table", "lyanou", "A detailed recreation of a billiards table, perfect for adding fun and sophistication to any interior.", "Pool table.jpg", 0];
        yield ["Chair", "lyanou", "A stylish, sturdy chair design, simple yet elegant, completing any room or outdoor space.", "Chair.png", 0];
    }
    
    /**
     * Generates initialization data for themes : [name, [tutorial1, tutorial2, ...]]
     * @return \\Generator
     */
    private static function themeGenerator()
    {
        yield ["Fantasy", ["Fantasy House", "Well", "Field"], "spokez", True];
        yield ["Fun", ["Clock", "Pool table"], "lyanou", False];
        yield ["House", ["Cupboard", "Clock", "Chair"], "lyanou", True];
    }
    
    public function load(ObjectManager $manager) : void
    {
        // Delete all images from public/images/screens
        $dirPath = __DIR__ . "/../../public/images/screens/";
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            unlink($file);
        }
        
        // Copy the the plus image file
        $source = __DIR__ . "/../../public/images/fixtures/plus.png";
        $destination = __DIR__ . "/../../public/images/screens/plus.png";
        copy($source, $destination);
        
        // Copy the tutorials, members and themes file
        $source = __DIR__ . "/../../public/images/fixtures/tutorials.png";
        $destination = __DIR__ . "/../../public/images/screens/tutorials.png";
        copy($source, $destination);
        
        $source = __DIR__ . "/../../public/images/fixtures/members.png";
        $destination = __DIR__ . "/../../public/images/screens/members.png";
        copy($source, $destination);
        
        $source = __DIR__ . "/../../public/images/fixtures/themes.png";
        $destination = __DIR__ . "/../../public/images/screens/themes.png";
        copy($source, $destination);
        
        
        $memberRepo = $manager->getRepository(Member::class);
        
        foreach (self::tutorialGenerator() as [$name, $member, $description, $imageName, $nbSteps])
        {
            // Copy the imageBuild from public/images/fixtures to public/images/screens
            $source = __DIR__ . "/../../public/images/fixtures/" . $imageName;
            $destination = __DIR__ . "/../../public/images/screens/" . $imageName;
            
            copy($source, $destination);
            
            // Creation of a tutorial
            $library = $memberRepo->findOneBy(['name' => $member])->getLibrary();
            
            $tutorial = new Tutorial();
            $tutorial->setName($name);
            $tutorial->setDescription($description);
            
            // Loading of imageBuild
            $imageBuild = new Image();
            $imageBuild->setImageName($imageName);
            $imageBuild->setImageSize(filesize($destination));
            $imageBuild->setUpdatedAt(new \DateTimeImmutable());
            
            $tutorial->setImageBuild($imageBuild);
            
            // Add of the screens of the steps
            for ($i = 1; $i <= $nbSteps; $i += 1)
            {
                $source = __DIR__ . "/../../public/images/fixtures/" . $name . " " . $i . ".png";
                $destination = __DIR__ . "/../../public/images/screens/" . $name . " " . $i . ".png";
                copy($source, $destination);
                
                $step = new Image();
                $step->setImageName($name . " " . $i . ".png");
                $step->setImageSize(filesize($destination));
                $step->setUpdatedAt(new \DateTimeImmutable());
                
                $tutorial->addStep($step);
                $manager->persist($step);
            }
            
            $library->addTutorial($tutorial);
            // There's a cascade persist on library
            $manager->persist($library);
        }
        $manager->flush();
        
        // Loading of Themes
        $tutorialRepo = $manager->getRepository(Tutorial::class);
        
        foreach (self::themeGenerator() as [$name, $tutorials, $member, $published])
        {
            $theme = new Theme();
            $theme->setName($name);
            $theme->setPublished($published);

            $user = $memberRepo->findOneBy(['name' =>  $member]);
            
            $user->addTheme($theme);

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
