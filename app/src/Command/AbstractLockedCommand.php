<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\PdoStore;

/**
 * Base class for locked commands.
 * Locked command cannot be executed twice at the same time.
 */
abstract class AbstractLockedCommand extends Command
{
    protected LockInterface $lock;
    protected string $lockResource;

    public function __construct()
    {
        if (empty($this->lockResource)) {
            throw new LogicException('You must set the lockResource property in the concrete command class.');
        }

        $factory = new LockFactory($this->getLockStore());
        $this->lock = $factory->createLock($this->lockResource);

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock->acquire()) {
            $this->handleLock($input, $output);

            return Command::SUCCESS;
        }

        $commandResult = $this->lockedExecute($input, $output);

        $this->lock->release();

        return $commandResult;
    }

    /**
     * This code will be performed if an another command is already running.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function handleLock(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Another command is running');
    }

    /**
     * Lock storage.
     *
     * @return PersistingStoreInterface
     */
    protected function getLockStore(): PersistingStoreInterface
    {
        //todo: config
        $databaseConnectionOrDSN = 'mysql:host=DbSymfony;dbname=symfony_test';
        $store = new PdoStore($databaseConnectionOrDSN, ['db_username' => 'root', 'db_password' => 'toor']);

        try {
            $store->createTable(); //todo: migration
        } catch (\Throwable $e) {

        }

        return $store;
    }

    /**
     * Your code will be here.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Exit code
     */
    abstract protected function lockedExecute(InputInterface $input, OutputInterface $output): int;
}
