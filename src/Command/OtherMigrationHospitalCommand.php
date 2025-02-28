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
    // таблица "откуда берем" -> к какой сущности привязываем
    // сделать условие если id то поиск этой сущности
    // это прям mi bombocla решение, но тут надо очень подумать
    private array $structConvert = [
        "patient"=>[
            'target' => Patients::class,
            "fields" => [
                "name" => [
                    "type"=>"string",
                    "setter" => "setName",
                    "source_fields" => ["name","last_name"],
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
                    'setter' => 'setChamber',
                    'source_fields' => ['ward_id']
                ],
                'patients' => [
                    'type' => Patients::class,
                    'setter' => 'setPatients',
                    'source_fields' => ['patient_id']
                ]
            ]
        ],
        'procedure' => [
            'target' => Procedures::class,
            'fields' => [
                'title' => [
                    'type' => "string",
                    'setter' => 'setName',
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
                    'setter' => 'setDescription',
                    'source_fields' => ['procedure_id']
                ],
                'source_id' => [
                    'type' => 'integer',
                    'setter' => 'setSourceId',
                    'source_fields' => ['ward_id']
                ],
                'source_type' => [
                    'type' => 'string',
                    'setter' => 'setSourceType',
                    'source_fields' => [],
                    'default' => 'chambers'
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

        // пробегаемся по массиву со структурой базы данных от куда будет брать данные
        foreach ($this->structConvert as $key => $struct) {
            $result = $this->newAdapterFabric($key, $struct);
        }
        $this->io->success('Migrate successfully ended');

        return Command::SUCCESS;
    }
    private function getRowsFromTable(string $tableName, string $tableColumn): array
    {
        $query = sprintf("SELECT %s from %s limit 5", $tableColumn, $tableName);

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
        $functionName = "create" . ucfirst($entityType);
        return $this->migrateTable($entityType,$structure);
    }
    private function migrateTable($entityType,$structure): bool
    {
        dump($entityType,$structure);
        $columnsForSearch =[];
        foreach ($structure['fields'] as $values){
            foreach ($values['source_fields'] as $sf) {
                $columnsForSearch[] = $sf;
            }
        }
        $columnsForSearch = implode(",", $columnsForSearch);
        $sourceItems = $this->getRowsFromTable($entityType, $columnsForSearch);
        if(count($sourceItems)==0){
            return false;
        }

        foreach ($sourceItems as $item){
            // проверка на существование обьектов в таргет базе данных
            $findObj = $this->getObjectFromRepository($item,$structure);

            if(count($findObj)>0){
                continue;
            }
            // если обьектов нет, то начинаем создавать
            // для создания обьекта нам нужно пробежать по fields обьекта и если, простой тип,
            // то украсть и создать,
            // но если там класс, то надо найти этот класс и вставить его как обьект
            // так же надо проверять на поле default - если оно есть, то в приоритете брать
            // с этого поля

            $newObj = new $structure['target'];
            foreach ($structure['fields'] as $field => $property){
                foreach ($property['source_fields'] as $sourceField){

                }
                $newObj->$property['setter']();
                dump($field);
                dd($property);
            }
        }

        return false;
    }
    private function getObjectFromRepository(array $item,array $structure): array
    {
        $objRepository = $this->entityManager->getRepository($structure['target']);
        $findByConfig = [];

        // заполнение полей для поиска из репозитория
        foreach ($structure['fields'] as $field => $property){
            if(class_exists($property['type'])){
                // если нашли класс в конфиге то обрабатываем по другому
            }
            else if((gettype($property['type'])==="string" )or (gettype($property['type'])==="integer")){
                // т.е. если тип строка или число
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

}
