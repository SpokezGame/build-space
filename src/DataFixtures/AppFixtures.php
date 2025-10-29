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
        yield ["Fantasy House", "spokez", "A cozy magical dwelling with enchanting details and mystical charm. It's perfect for adventurers seeking comfort and wonder.", "Fantasy House.jpg"];
        yield ["Well", "spokez", "A rustic stone well that brings life and realism to any village or medieval courtyard.", "Well.png"];
        yield ["Cupboard", "lyanou", "A charming wooden cupboard with fine detailing, a small but elegant touch of homely design.", "Cupboard.jpg"];
        yield ["Field", "spokez", "A peaceful stretch of green farmland, ideal for crops, animals, or a tranquil countryside vibe.", "Field.jpg"];
        yield ["Clock", "lyanou", "An ornate clock structure showcasing craftsmanship and precision, time stands still in its beauty.", "Clock.jpeg"];
        yield ["Pool table", "lyanou", "A detailed recreation of a billiards table, perfect for adding fun and sophistication to any interior.", "Pool table.jpg"];
        yield ["Chair", "lyanou", "A stylish, sturdy chair design, simple yet elegant, completing any room or outdoor space.", "Chair.png"];
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

        foreach (self::tutorialGenerator() as [$name, $member, $description, $imageName])
        {
            // Copy the image from public/images/fixtures to public/images/screens
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
            
            // Dossier contenant les images
            $imageDir = __DIR__ . '/../../public/images/screens';
            
            $imageBuild->setImageSize(filesize($imageDir . '/' . $imageName));
            $imageBuild->setUpdatedAt(new \DateTimeImmutable());
            
            $tutorial->setImageBuild($imageBuild);
            
            $library->addTutorial($tutorial);
            // there's a cascade persist on library
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
