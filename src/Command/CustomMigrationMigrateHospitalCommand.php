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
    name: 'custom:migration:migrate:hospital',
    description: 'Add a short description for your command',
)]
class CustomMigrationMigrateHospitalCommand extends Command
{
    // описываеться структура базы данных откуда брать миграцию
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

        // пробигаемся по массиву со структурой базы данных от куда будет брать данные
        foreach ($this->dbStruct as $key => $struct) {
            $result = $this->adapterFabric($key, $struct);
        }
        dd();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }


    private function adapterFabric(string $entityType, array $structure): bool
    {
        $functionName = "create" . ucfirst($entityType);
        if (method_exists($this, $functionName)) {
            return $this->$functionName($entityType, $structure);
        } else {
            return false;
        }
    }

    private function getRowsFromTable(string $tableName, string $tableColumn): array
    {
        $query = sprintf("SELECT %s from %s limit 3", $tableColumn, $tableName);

        return $this->makeQuery($query);
    }

    private function createPatient(string $tableName, array $structure): bool
    {
        $columns = implode(",", $structure);
        $sourcePatients = $this->getRowsFromTable($tableName, $columns);
        if (count($sourcePatients) <= 0) {
            return false;
        }
        foreach ($sourcePatients as $sp){
            $patientsRepository = $this->entityManager->getRepository(Patients::class);
            $foundedPatients = $patientsRepository->findBy([
                "name" =>$sp['name'].' '.$sp['last_name'],
                "card_number" => $sp['card_number']
            ]);
            if(count($foundedPatients)>0){
                $this->io->text("Patient exists - ".$sp['name'].' '.$sp['last_name']);
                continue;
            }
            $newPatient = new Patients();
            $newPatient->setName($sp['name'].' '.$sp['lastname']);
            $newPatient->setCardNumber($sp['card_number']);

            $this->entityManager->persist($newPatient);
        }
        $this->entityManager->flush();
        $this->io->success('Patients has been migrate');
        return true;
    }
    private function createWard(string $tableName, array $structure): bool
    {
        $columns = implode(",", $structure);
        $sourceWards = $this->getRowsFromTable($tableName, $columns);
        if (count($sourceWards) <= 0) {
            return false;
        }

        foreach ($sourceWards as $sw){
            $chambersRepository = $this->entityManager->getRepository(Chambers::class);

            $foundChambers = $chambersRepository->findBy([
               "number" => $sw['ward_number']
            ]);
            if(count($foundChambers)>0){
                $this->io->text("Chamber exists, number - ".$sw['ward_number']);
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
        $columns = implode(",", $structure);
        $hospitalizations = $this->getRowsFromTable($tableName, $columns);
        if (count($hospitalizations) <= 0) {
            return false;
        }
        foreach ($hospitalizations as $hz) {
            $chambersPatientsRepository = $this->entityManager->getRepository(ChambersPatients::class);
            // тут вот интересно потому что там привязка локальная и id другие, т.е. нам надо
            // в той базе данных получить пациента, и получить ward и уже сдесь в локальной подготовить запрос

            $chambersPatients = $chambersPatientsRepository->findBy([

            ]);
        }

        return true;
    }
    private function createProcedure(string $tableName, array $structure): bool
    {
        return false;
    }

    private function createWard_procedure(string $tableName, array $structure): bool
    {
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

/*
    // таблица "откуда берем" -> к какой сущности привязываем
    // сделать условие если id то поиск этой сущности
    // это прям mi bombocla решение, но тут надо очень подумать
    private array $structConvert = [
        "patient"=>[
            'target' => Patients::class,
            "fields" => [
                "name" => "name",
                "last_name" => "name",
                'card_number' => 'card_number'
            ]
        ],
        "ward" => [
            'target' => Chambers::class,
            'fields' => [
                'ward_number' => 'number'
            ]
        ],
        'hospitalization' => [
            'target' => ChambersPatients::class,
            'fields' => [
                'ward_id' => 'chambers',
                'patient_id' => 'patients'
            ]
        ],
        'procedure' => [
            'target' => Procedures::class,
            'fields' => [
                'name' => 'title',
                'description' => 'description'
            ]
        ],
        'ward_procedure' => [
            'target' => ProcedureList::class,
            'fields' => [
                'procedure_id' => 'procedures',
                'ward_id' => 'source_id',
                '!chamber' => 'source_type'

            ]
        ]
    ];

// это просто миграция которая универсальная, я ее устал делать поэтому ща просто сделаю
// на нужнную бд и все
//private function migrate(string $tableName,array $structure): bool
//    {
//        $columns = implode(",",$structure['fields']);
//        $sourceRow = $this->getRowsFromTable($tableName,"*");
//        foreach ($sourceRow as $sr){
//            $newObj = new $structure['target']();
//            foreach ($structure['fields'] as $sourceFiled => $targetField) {
//                dump($sr);
//                if(str_contains($targetField,'_')){
//                    $targetField = explode('_',$targetField);
//                    $targetField = implode('',array_map('ucfirst',$targetField));
//                }
//                else {
//                    $targetField = ucfirst($targetField);
//                }
//                $setterField = "set".$targetField;
//                $getterField = "get".$targetField;
//                $newObj->$setterField($newObj->$getterField()?$newObj->$getterField().' '.$sr[$sourceFiled]:$sr[$sourceFiled]);
//
//            }
//            dump($newObj);
//        }
////        dd($sourceRow);
////        $columns = implode(",",$structure);
////        $patients = $this->getRowsFromTable($tableName,$columns);
////        if(count($patients)<=0){
////            return false;
////        }
////        foreach ($patients as $pt){
////            // check field in database, because he maybe exists
////            $patientsRepository = $this->entityManager->getRepository(Patients::class);
////            $findPatients= $patientsRepository->findBy([
////                "name" => $pt['name'].' '.$pt['last_name'],
////                "card_number" => $pt['card_number']
////            ]);
////            if(count($findPatients)>0){
////                continue;
////            }
////            $newPatient = new Patients();
////            $newPatient->setName($pt['name'].' '.$pt['last_name']);
////            $newPatient->setCardNumber($pt['card_number']);
////            $this->entityManager->persist($newPatient);
////        }
////        $this->entityManager->flush();
//
//        return true;
//    }

*/