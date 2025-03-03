<?php

namespace App\Command;

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
    name: 'app:migration:migrate:hospital',
    description: 'Migrate from hospital database to main ',
)]
class CustomMigrationMigrateHospitalCommand extends Command
{
    // описывается структура базы данных откуда брать миграцию
    // порядок важен, порядок - очередь обработки
    private array $dbStruct = [
        "patient" => [
            "id",
            "name",
            "last_name",
            "card_number"
        ],
        "ward" => [
            "id",
            "ward_number"
        ],
        "hospitalization" => [
            "id",
            "patient_id",
            "ward_id"
        ],
        "procedure" => [
            "id",
            "name",
            "description"
        ],
        "ward_procedure" => [
            "id",
            "ward_id",
            "procedure_id",
            "sequence"
        ]
    ];
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
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

        foreach ($this->dbStruct as $tableName => $structure) {
            $this->migrateTable($tableName, $structure);
        }
        $this->io->success('Migrate successfully ended');

        return Command::SUCCESS;
    }


    private function migrateTable(string $entityType, array $structure): void
    {
        $functionName = "create" . ucfirst($entityType);
        if (method_exists($this, $functionName)) {
            $this->$functionName($entityType, $structure);
        } else {
            $this->io->error('Check configuration');
        }

    }

    private function getRowsFromTable(string $tableName, string $tableColumn): array
    {
        $query = sprintf("SELECT %s from %s limit 5", $tableColumn, $tableName);

        return $this->makeQuery($query);
    }

    private function getSourceItems(string $tableName, $structure): array
    {
        $columns = implode(",", $structure);

        return $this->getRowsFromTable($tableName, $columns);
    }

    private function createPatient(string $tableName, array $structure): bool
    {
        $sourcePatients = $this->getSourceItems($tableName, $structure);
        if (count($sourcePatients) <= 0) {
            return false;
        }
        foreach ($sourcePatients as $sp) {
            $patientsRepository = $this->entityManager->getRepository(Patients::class);
            $foundedPatients = $patientsRepository->findBy([
                "name" => $sp['name'] . ' ' . $sp['last_name'],
                "card_number" => $sp['card_number']
            ]);
            if (count($foundedPatients) > 0) {
                $this->io->text("Patient exists - " . $sp['name'] . ' ' . $sp['last_name']);
                continue;
            }
            $newPatient = new Patients();
            $newPatient->setName($sp['name'] . ' ' . $sp['last_name']);
            $newPatient->setCardNumber($sp['card_number']);

            $this->entityManager->persist($newPatient);

        }
        $this->entityManager->flush();
        $this->io->success('Patients has been migrate');

        return true;
    }

    private function createWard(string $tableName, array $structure): bool
    {
        $sourceWards = $this->getSourceItems($tableName, $structure);
        if (count($sourceWards) <= 0) {
            return false;
        }

        foreach ($sourceWards as $sw) {
            $chambersRepository = $this->entityManager->getRepository(Chambers::class);

            $foundChambers = $chambersRepository->findBy([
                "number" => $sw['ward_number']
            ]);
            if (count($foundChambers) > 0) {
                $this->io->text("Chamber exists, number - " . $sw['ward_number']);
                continue;
            }
            $newChamber = new Chambers();
            $newChamber->setNumber($sw['ward_number']);

            $this->entityManager->persist($newChamber);
        }
        $this->entityManager->flush();
        $this->io->success('Chambers has been migrate');

        return false;
    }

    private function createHospitalization(string $tableName, array $structure): bool
    {
        $hospitalizations = $this->getSourceItems($tableName, $structure);
        if (count($hospitalizations) <= 0) {
            return false;
        }
        foreach ($hospitalizations as $hz) {
            $newChambersPatients = new ChambersPatients();
            $chambersPatientsRepository = $this->entityManager->getRepository(ChambersPatients::class);
            $patientsRepository = $this->entityManager->getRepository(Patients::class);
            $chamberRepository = $this->entityManager->getRepository(Chambers::class);

            $patient = $this->makeQuery("Select name,last_name,card_number from patient where id=" . $hz['patient_id']);
            if (count($patient) <= 0) {
                $this->io->text("Source patient not found - skip");
                continue;
            }
            $foundPatient = $patientsRepository->findBy([
                "name" => $patient[0]['name'] . ' ' . $patient[0]['last_name'],
                "card_number" => $patient[0]['card_number'],
            ]);
            if (count($foundPatient) <= 0) {
                $this->io->text("Target patient not found - skip");
                continue;
            }
            $chambers = $this->makeQuery("Select ward_number from ward where id=" . $hz['ward_id']);
            if (count($chambers) <= 0) {
                $this->io->text("Source chambers not found - skip");
                continue;
            }
            $foundChamber = $chamberRepository->findBy([
                "number" => $chambers[0]['ward_number']
            ]);
            if (count($foundChamber) <= 0) {
                $this->io->text("Chamber not found - skip");
                continue;;
            }
            $foundChamberPatients = $chambersPatientsRepository->findBy([
                "chambers" => $foundChamber[0],
                "patients" => $foundPatient[0]
            ]);
            if (count($foundChamberPatients) > 0) {
                $this->io->text("ChamberPatients - has exists");
                continue;
            }
            // если ее нет, то создаем новую запись
            $newChambersPatients->setChambers($foundChamber[0]);
            $newChambersPatients->setPatients($foundPatient[0]);

            $this->entityManager->persist($newChambersPatients);

        }
        $this->entityManager->flush();
        $this->io->success('ChamberPatients has been migrate');

        return true;
    }

    private function createProcedure(string $tableName, array $structure): bool
    {
        $sourceProcedures = $this->getSourceItems($tableName, $structure);
        if (count($sourceProcedures) <= 0) {
            return false;
        }
        foreach ($sourceProcedures as $sp) {
            $procedureRepository = $this->entityManager->getRepository(Procedures::class);
            $foundProcedure = $procedureRepository->findBy([
                "title" => $sp['name'],
                "description" => $sp['description']
            ]);
            // если такая процедура найдена, то скипаем
            if (count($foundProcedure) > 0) {
                $this->io->text('Procedure is exists, title - ' . $sp['name']);
                continue;
            }
            $newProcedure = new Procedures();
            $newProcedure->setTitle($sp['name']);
            $newProcedure->setDescription($sp['description']);

            $this->entityManager->persist($newProcedure);
        }
        $this->entityManager->flush();
        $this->io->success('Procedures has been migrated');

        return true;
    }

    private function createWard_procedure(string $tableName, array $structure): bool
    {
        // работаем с ProcedureList
        $procedureListRepository = $this->entityManager->getRepository(ProcedureList::class);
        $chamberRepository = $this->entityManager->getRepository(Chambers::class);
        $procedureRepository = $this->entityManager->getRepository(Procedures::class);
        $sourceWardProcedures = $this->getSourceItems($tableName, $structure);
        foreach ($sourceWardProcedures as $swp) {
            $sourceFoundProcedure = $this->makeQuery("Select * from procedure where id=" . $swp['procedure_id']);
            $sourceFoundWard = $this->makeQuery("Select ward_number from ward where id=" . $swp['ward_id']);
            if (count($sourceFoundProcedure) <= 0) {
                continue;
            }

            $foundProcedures = $procedureRepository->findBy([
                "title" => $sourceFoundProcedure[0]['name'],
                "description" => $sourceFoundProcedure[0]['description']
            ]);
            if (count($foundProcedures) <= 0) {
                $this->io->text('Procedure not found in target db - skip');
                continue;
            }
            $findChamber = $chamberRepository->findBy([
                'number' => $sourceFoundWard[0]['ward_number']
            ]);
            if (count($findChamber) <= 0) {
                $this->io->text('Chamber not found - skip');
                continue;
            }
            $findProcedureList = $procedureListRepository->findBy([
                'procedures' => $foundProcedures[0],
                'source_type' => 'chambers',
                'source_id' => $swp['ward_id']
            ]);
            if (count($findProcedureList) > 0) {
                $this->io->text("Procedure list is exists - skip");
                continue;
            }
            $newProcedureList = new ProcedureList();
            $newProcedureList->setProcedures($foundProcedures[0]);
            $newProcedureList->setStatus(false);
            $newProcedureList->setQueue($swp['sequence']);
            $newProcedureList->setSourceId($swp['ward_id']);
            $newProcedureList->setSourceType('chambers');
            $this->entityManager->persist($newProcedureList);

        }
        $this->entityManager->flush();
        $this->io->success('Ward_procedure has been migrated');

        return false;
    }

    private function makeQuery(string $sql): array
    {
        $dsn = 'pgsql:host=localhost;port=5433;dbname=hospital;user=symfony_user;password=symfony_password';
        $pdo = new PDO($dsn);
        $result = $pdo->query($sql);
        $pdo = null;

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

}
