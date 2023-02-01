<?php

namespace App\Command;

use App\Services\ProductImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-products',
    description: 'Add a short description for your command',
)]
class ImportProductsCommand extends AbstractLockedCommand
{
    protected string $lockResource = 'import-products';
    protected EntityManagerInterface $entityManager;
    protected ProductImporter $productImporter;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productImporter = new ProductImporter($this->entityManager);

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'File address')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function lockedExecute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument('url');

        //todo: UrlValidator
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $io->note('Wrong url');
            return Command::FAILURE;
        }

        //todo: error handling, logging
        $importResult = $this->productImporter->import($url);

        $io->success(sprintf('Added: %s. Updated: %s.', $importResult['added'], $importResult['updated']));

        return Command::SUCCESS;
    }
}
