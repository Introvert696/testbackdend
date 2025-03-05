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
    private array $databaseStructure = [
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
                    'type' => 'id',
                    'setter' => 'setSourceId',
                    'source_table' => 'ward',
                    'source_fields' => ['ward_id'],
                    'field_for_relations' => 'ward_number',
                    'field_for_search' => 'number',
                    'relation_entity' => Chambers::class
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
    private array $usedData;
    private int $successCount = 0;
    private int $failureCount = 0;

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
        foreach ($this->databaseStructure as $key => $struct) {
            $this->migrateTable($key, $struct);
            $this->responseInfo();
            $this->resetCounts();
        }
        $this->io->success('Migrate ended');

        return Command::SUCCESS;
    }

    private function migrateTable($entityType, $structure): void
    {
        $columnsForSearch = $this->fillArrayToSearch($structure);
        $sourceItems = $this->getRowsFromTable($entityType, $columnsForSearch);
        if (count($sourceItems) == 0) {
            return;
        }
        $createdItems = $this->createNewItems($sourceItems, $structure);
        $this->saveItems($createdItems);
        $this->io->success('Successfully migrate table - ' . $entityType);
    }

    private function fillArrayToSearch($structure): string
    {
        $columnsForSearch = [];
        foreach ($structure['fields'] as $values) {
            foreach ($values['source_fields'] as $sf) {
                $columnsForSearch[] = $sf;
            }
        }

        return implode(",", $columnsForSearch);
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

    // item
    private function createNewItems($sourceItems, $structure): array
    {
        $createdItems = [];
        foreach ($sourceItems as $item) {
            $newItem = $this->createNewTargetItem($item, $structure);
            if (!$newItem) {
                continue;
            }
            $createdItems[] = $newItem;
        }

        return $createdItems;
    }

    // item
    private function createNewTargetItem($item, $structure): object|bool
    {
        $findItems = $this->getItemsFromRepository($item, $structure);
        if (count($findItems) > 0) {
            $this->failureCount++;

            return false;
        }
        $newItem = $this->fillFieldsForCreate($structure, $item);
        $duplicateItems = $this->findByFromRepository($structure['target'], $this->usedData);
        $this->usedData = [];
        if (count($duplicateItems) > 0) {
            $this->failureCount++;

            return false;
        }

        return $newItem;
    }

    private function getItemsFromRepository(array $item, array $structure): array
    {
        $objRepository = $this->entityManager->getRepository($structure['target']);
        $findByConfig = [];
        foreach ($structure['fields'] as $field => $property) {
            if (class_exists($property['type'])) {
                $findObjectForSearch = $this->findObjectFromRepositoryWithClassTypeById(
                    $property['type'],
                    $item[$property['source_fields'][0]]);
                $findByConfig[$field] = $findObjectForSearch;
            } else {
                foreach ($property['source_fields'] as $sf) {
                    if (isset($findByConfig[$field])) {
                        $findByConfig[$field] = $findByConfig[$field] . ' ' . $item[$sf];
                    } else {
                        $findByConfig[$field] = $item[$sf];
                    }
                }
            }
        }

        return $objRepository->findBy($findByConfig);
    }

    private function findObjectFromRepositoryWithClassTypeById(string $classname, int $id): mixed
    {
        $repositoryForSearch = $this->entityManager->getRepository($classname);

        return $repositoryForSearch->findOneBy(
            ["id" => $id]
        );
    }

    private function fillFieldsForCreate(array $structure, mixed $item): object
    {
        $newObject = new $structure['target'];
        $fields = $structure['fields'];
        foreach ($fields as $field => $property) {
            $valueToSet = "";
            if (class_exists($property['type'])) {
                $findingObjFromSource = $this->findItemFromSourceById($item, $property);
                $searchConfig = $this->createSearchConfig($findingObjFromSource, $property['fields_for_search']);
                $objRepository = $this->entityManager->getRepository($property['type']);
                $findByTarget = $objRepository->findBy($searchConfig);
                if (count($findByTarget) <= 0) {
                    break;
                }
                $valueToSet = $findByTarget[0];
            } else if ($property['type'] === "default") {
                $valueToSet = $property['default'];
            } else if ($property['type'] === 'id') {
                $sqlForSearch = "Select * from " . $property['source_table'] . ' where';
                foreach ($property['source_fields'] as $source_field) {
                    $sqlForSearch .= ' id=' . $item[$source_field];
                }
                $searchResult = $this->makeQuery($sqlForSearch);
                if (count($searchResult) <= 0) {
                    break;
                }
                $valueToSet = $searchResult[0];
                $searchData[$property['field_for_search']] = $valueToSet[$property['field_for_relations']];
                $targetRepository = $this->entityManager->getRepository($property['relation_entity']);
                $findObject = $targetRepository->findBy($searchData);
                if (count($findObject) <= 0) {
                    break;
                }
                $findObject = $findObject[0];
                $valueToSet = $findObject->getId();

            } else {
                foreach ($property['source_fields'] as $sourceField) {
                    $valueToSet === "" ?
                        $valueToSet = $item[$sourceField] :
                        $valueToSet = $valueToSet . ' ' . $item[$sourceField];
                }
            }
            $setter = $property['setter'];
            $newObject->$setter($valueToSet);
            $this->usedData[$field] = $valueToSet;
        }

        return $newObject;
    }

    private function findItemFromSourceById($item, $property): mixed
    {
        $sourceObjectId = $item[$property['source_fields'][0]];
        $sqlForSearch = "Select * from " . $property['source_table'] . ' where id =' . $sourceObjectId;

        return $this->makeQuery($sqlForSearch)[0];
    }

    private function createSearchConfig($findingObjFromSource, $fieldsForSearch): array
    {
        $searchConfig = [];
        foreach ($fieldsForSearch as $key => $fieldValue) {
            if (!isset($searchConfig[$fieldValue])) {
                $searchConfig[$fieldValue] = $findingObjFromSource[$key];
            } else {
                $searchConfig[$fieldValue] .= ' ' . $findingObjFromSource[$key];
            }
        }

        return $searchConfig;
    }

    private function findByFromRepository(string $classname, array $params): array
    {
        // TODO - добавить поиск по каждому полю и по всем
        $objectRepository = $this->entityManager->getRepository($classname);

        return $objectRepository->findBy($params);
    }

    private function saveItems($createdItems): void
    {
        foreach ($createdItems as $item) {
            $this->entityManager->persist($item);
            $this->successCount += 1;
        }
        $this->entityManager->flush();
    }

    private function responseInfo(): void
    {
        $this->io->title(
            "Count: " . $this->successCount + $this->failureCount .
            ". Success: " . $this->successCount .
            ". Skip: " . $this->failureCount
        );

    }

    private function resetCounts(): void
    {
        $this->successCount = 0;
        $this->failureCount = 0;
    }

}
