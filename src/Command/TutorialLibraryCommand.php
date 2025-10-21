<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\TutorialLibraryRepository;
use App\Entity\TutorialLibrary;

#[AsCommand(
    name: 'app:tutorial-library',
    description: 'Command that shows all the tutorial libraries',
)]
class TutorialLibraryCommand extends Command
{
    private ?TutorialLibraryRepository $tutorialLibraryRepository;
    
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->tutorialLibraryRepository = $doctrineManager->getRepository(TutorialLibrary::class);
        
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $tutorialLibrary = $this->tutorialLibraryRepository->findAll();
        if (! $tutorialLibrary) {
            $io->error('No list of tutorials were found!');
            return Command::FAILURE;
        } else {
            $io->title('List of list of tutorials :');
            
            $io->listing($tutorialLibrary);
            
            return Command::SUCCESS;
        }
    }
}
