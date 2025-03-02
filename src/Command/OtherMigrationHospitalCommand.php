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
    name: 'app:migration:migrate',
    description: 'Migrate the some database',
)]
class OtherMigrationHospitalCommand extends Command
{
    // конфигурация для миграции базы данных
    private array $structConvert = [
        "patient" => [
            'target' => Patients::class,
            "fields" => [
                "name" => [
                    "type" => "string",
                    "setter" => "setName",
                    "source_fields" => ["name", "last_name"],
                ],
                "card_number" => [
                    'type' => 'integer',
                    'setter' => 'setCardNumber',
                    'source_fields' => ['card_number']
                ],
            ]
        ],
        "ward" => [
            'target' => Chambers::class,
            'fields' => [
                'number' => [
                    'type' => 'string',
                    'setter' => 'setNumber',
                    'source_fields' => ['ward_number']
                ]
            ]
        ],
        'hospitalization' => [
            'target' => ChambersPatients::class,
            'fields' => [
                'chambers' => [
                    'type' => Chambers::class,
                    'setter' => 'setChambers',
                    'source_fields' => ['ward_id'],
                    'source_table' => 'ward',
                    'fields_for_search' => [
                        'ward_number' => "number"
                    ],

                ],
                'patients' => [
                    'type' => Patients::class,
                    'setter' => 'setPatients',
                    'source_fields' => ['patient_id'],
                    'source_table' => 'patient',
                    'fields_for_search' => [
                        'name' => 'name',
                        'last_name' => 'name',
                        'card_number' => 'card_number',
                    ],
                ]
            ]
        ],
        'procedure' => [
            'target' => Procedures::class,
            'fields' => [
                'title' => [
                    'type' => "string",
                    'setter' => 'setTitle',
                    'source_fields' => ['name']
                ],
                'description' => [
                    'type' => "string",
                    'setter' => 'setDescription',
                    'source_fields' => ['description']
                ]
            ]
        ],
        'ward_procedure' => [
            'target' => ProcedureList::class,
            'fields' => [
                'procedures' => [
                    'type' => Procedures::class,
                    'setter' => 'setProcedures',
                    'source_fields' => ['procedure_id'],
                    'source_table' => 'procedure',
                    'fields_for_search' => [
                        'name' => "title",
                        'description' => "description",
                    ],
                ],
                'source_id' => [
                    'type' => 'integer',
                    'setter' => 'setSourceId',
                    'source_fields' => ['ward_id']
                ],
                'source_type' => [
                    'type' => 'default',
                    'setter' => 'setSourceType',
                    'source_fields' => [],
                    'default' => 'chambers'
                ],
                'queue' => [
                    'type' => 'integer',
                    'setter' => 'setQueue',
                    'source_fields' => ['sequence'],
                ]
            ]
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
        foreach ($this->structConvert as $key => $struct) {
            $this->newAdapterFabric($key, $struct);
        }
        $this->io->success('Migrate successfully ended');

        return Command::SUCCESS;
    }

    private function getRowsFromTable(string $tableName, string $tableColumn): array
    {
        $query = sprintf("SELECT %s from %s", $tableColumn, $tableName);

        return $this->makeQuery($query);
    }

    private function makeQuery(string $sql): array
    {
        $dsn = 'pgsql:host=localhost;port=5433;dbname=hospital;user=symfony_user;password=symfony_password';
        $pdo = new PDO($dsn);
        $result = $pdo->query($sql);
        $pdo = null;

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    private function newAdapterFabric(string $entityType, array $structure): bool
    {
        return $this->migrateTable($entityType, $structure);
    }

    private function migrateTable($entityType, $structure): bool
    {

        $columnsForSearch = [];
        foreach ($structure['fields'] as $values) {
            foreach ($values['source_fields'] as $sf) {
                $columnsForSearch[] = $sf;
            }
        }
        $columnsForSearch = implode(",", $columnsForSearch);
        $sourceItems = $this->getRowsFromTable($entityType, $columnsForSearch);
        if (count($sourceItems) == 0) {
            return false;
        }

        foreach ($sourceItems as $item) {
            $usedData = [];
            $findObj = $this->getObjectFromRepository($item, $structure);
            if (count($findObj) > 0) {
                continue;
            }
            $newObj = new $structure['target'];
            foreach ($structure['fields'] as $field => $property) {
                $valueToSet = "";

                if (class_exists($property['type'])) {
                    $searchConfig = [];
                    $sourceObjectId = $item[$property['source_fields'][0]];
                    $findingObjFromSource = $this->makeQuery(
                        "Select * from " . $property['source_table'] . ' where id=' . $sourceObjectId
                    )[0];
                    foreach ($property['fields_for_search'] as $key => $fieldsForSearch) {
                        if (!$searchConfig[$fieldsForSearch]) {
                            $searchConfig[$fieldsForSearch] = $findingObjFromSource[$key];
                        } else {
                            $searchConfig[$fieldsForSearch] .= ' ' . $findingObjFromSource[$key];
                        }
                    }
                    $objRepository = $this->entityManager->getRepository($property['type']);
                    $findByTarget = $objRepository->findBy($searchConfig);
                    if (count($findByTarget) <= 0) {
                        break;
                    }
                    $valueToSet = $findByTarget[0];


                }
                else if ($property['type']==="default"){
                    $valueToSet = $property['default'];
                }
                else {
                    foreach ($property['source_fields'] as $sourceField) {
                        $valueToSet === "" ?
                            $valueToSet = $item[$sourceField] :
                            $valueToSet = $valueToSet . ' ' . $item[$sourceField];
                    }
                }
                $setter = $property['setter'];
                $newObj->$setter($valueToSet);
                $usedData[$field] = $valueToSet;
            }
            $duplicateObject = $this->findByFromRepository($structure['target'],$usedData);
            if (count($duplicateObject) > 0) {
                continue;
            }
            $this->entityManager->persist($newObj);
        }

        $this->entityManager->flush();

        $this->io->success('Migrate successfully, table - ' . $entityType);
        return false;
    }

    private function findByFromRepository(string $classname, array $params): array
    {
        $objectRepository =$this->entityManager->getRepository($classname);
        return $objectRepository->findBy($params);
    }
    private function getObjectFromRepository(array $item,array $structure): array
    {
        $objRepository = $this->entityManager->getRepository($structure['target']);
        $findByConfig = [];

        // заполнение полей для поиска из репозитория
        foreach ($structure['fields'] as $field => $property){
            if(class_exists($property['type'])){
                $findObjectForSearch= $this->findObjectFromRepositoryWithClassTypeById(
                    $property['type'],
                    $item[$property['source_fields'][0]]);
                $findByConfig[$field] = $findObjectForSearch;
            }
            else if((gettype($property['type'])==="string" )or (gettype($property['type'])==="integer")){
                foreach($property['source_fields'] as $sf){
                    if(isset($findByConfig[$field])){
                        $findByConfig[$field] = $findByConfig[$field].' '.$item[$sf];
                    }
                    else{
                        $findByConfig[$field] = $item[$sf];
                    }
                }
            }
        }
        return $objRepository->findBy($findByConfig);
    }

    /**
     * @param string $classname Classname for get a repository
     * @param int $id id for search
     * @return array
     */
    private function findObjectFromRepositoryWithClassTypeById(string $classname,int $id):mixed
    {
        $repositoryForSearch = $this->entityManager->getRepository($classname);
        return $repositoryForSearch->findOneBy(
            ["id" => $id]
        );
    }

}
