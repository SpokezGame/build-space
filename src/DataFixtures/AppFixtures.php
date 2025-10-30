<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Library;
use App\Entity\Theme;
use App\Entity\Tutorial;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Generates initialization data for members :
     *  [email, plain text password]
     * @return \\Generator
     */
    private function membersGenerator()
    {
        yield ['admin@localhost','123456', 'admin'];
        yield ['spokez@localhost','123456', 'spokez'];
        yield ['lyanou@localhost','123456', 'lyanou'];
    }
    
    /**
     * Generates initialization data for tutorials : [name, author]
     * @return \\Generator
     */
    private static function tutorialGenerator()
    {
        yield ["Fantasy House", "spokez", "A cozy magical dwelling with enchanting details and mystical charm. It's perfect for adventurers seeking comfort and wonder.", "Fantasy House.jpg", 21];
        yield ["Well", "spokez", "A rustic stone well that brings life and realism to any village or medieval courtyard.", "Well.png", 0];
        yield ["Cupboard", "lyanou", "A charming wooden cupboard with fine detailing, a small but elegant touch of homely design.", "Cupboard.jpg", 0];
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
        
        //  Loading of members
        foreach ($this->membersGenerator() as [$email, $plainPassword, $name]) {
            $user = new Member();
            $password = $this->hasher->hashPassword($user, $plainPassword);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setName($name);

            $library = new Library();
            $library->setMember($user);

            // $roles = array();
            // $roles[] = $role;
            // $user->setRoles($roles);

            $manager->persist($user);
        }
        $manager->flush();
        
        
        // Loading of Tutorials
        $userRepo = $manager->getRepository(Member::class);

        foreach (self::tutorialGenerator() as [$name, $member, $description, $imageName, $nbSteps])
        {
            // Copy the imageBuild from public/images/fixtures to public/images/screens
            $source = __DIR__ . "/../../public/images/fixtures/" . $imageName;
            $destination = __DIR__ . "/../../public/images/screens/" . $imageName;
            
            copy($source, $destination);
            
            // Creation of a tutorial
            $library = $userRepo->findOneBy(['name' => $member])->getLibrary();
            
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

            $user = $userRepo->findOneBy(['name' =>  $member]);
            
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
