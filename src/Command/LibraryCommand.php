<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\LibraryRepository;
use App\Entity\Library;

#[AsCommand(
    name: 'app:tutorial-library',
    description: 'Command that shows all the tutorial libraries',
)]
class LibraryCommand extends Command
{
    private ?LibraryRepository $libraryRepository;
    
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->libraryRepository = $doctrineManager->getRepository(Library::class);
        
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $library = $this->libraryRepository->findAll();
        if (! $library) {
            $io->error('No list of tutorials were found!');
            return Command::FAILURE;
        } else {
            $io->title('List of list of tutorials :');
            
            $io->listing($library);
            
            return Command::SUCCESS;
        }
    }
}
