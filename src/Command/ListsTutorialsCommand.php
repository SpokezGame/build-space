<?php

namespace App\Command;

use App\Entity\ListTutorials;
use App\Repository\ListTutorialsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:lists-tutorials',
    description: 'Command that shows list of list of tutorials',
)]
class ListsTutorialsCommand extends Command
{
    private ?ListTutorialsRepository $listTutorialsRepository;
    
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->listTutorialsRepository = $doctrineManager->getRepository(ListTutorials::class);
        
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $listTutorials = $this->listTutorialsRepository->findAll();
        if (! $listTutorials) {
            $io->error('No list of tutorials were found!');
            return Command::FAILURE;
        } else {
            $io->title('List of list of tutorials :');
            
            $io->listing($listTutorials);
            
            return Command::SUCCESS;
        }
    }
}
