<?php

namespace App\Command\MigratorCommand;

use App\Entity\Chambers;
use App\Entity\ChambersPatients;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migration:main',
    description: 'Migrate the some database',
)]
class MainMigrator extends Command
{
    private SymfonyStyle $io;
    private array $databaseStructure;

    public function __construct(
        private readonly DataReader    $dataReader,
        private readonly DataProcessor $dataProcessor,
        private readonly DataWriter    $dataWriter
    )
    {
        parent::__construct();
        $this->databaseStructure = ConfigMigrator::$databaseStructure;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->migrateTable($this->databaseStructure);
        $this->io->success('Migrate successfully ended');

        return Command::SUCCESS;
    }

    public function migrateTable($databaseStructure): void
    {
        foreach ($databaseStructure as $tableName => $tableStructure) {
            // find all item from source db
            $findingItemsFromSourceDatabase = $this->dataReader->findItemsFromSourceDatabase($tableName);
            // if item count <= 0, be nothing
            if (count($findingItemsFromSourceDatabase) === 0) {
                // if table is empty skip this table

                continue;
            }
            // else transform source item to target entity
            // in transform check every item in target database on duplicate
            $transformedItems = $this->dataProcessor->transformSourceItemsToEntity(
                $findingItemsFromSourceDatabase,
                $tableStructure
            );

            // save data in database
            $this->dataWriter->saveItems($transformedItems);
            // after transform, save every transformed data in database
            $this->responseInfo();
        }
    }

    private function responseInfo(): void
    {
        $this->io->title(
            "Count: " . ConfigMigrator::$successCount + ConfigMigrator::$failureCount .
            ". Success: " . ConfigMigrator::$successCount .
            ". Skip: " . ConfigMigrator::$failureCount
        );
        ConfigMigrator::resetCounts();
    }
}
