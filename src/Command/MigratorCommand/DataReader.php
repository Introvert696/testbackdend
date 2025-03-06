<?php

namespace App\Command\MigratorCommand;

use Doctrine\ORM\EntityManagerInterface;
use PDO;

class DataReader
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function findItemsFromSourceDatabase($tableName): array
    {
        $query = "Select * from " . $tableName;

        return $this->makeQuery($query);
    }

    public function findItemFromTargetDatabaseByConfig($entity, $config): array
    {
        $repository = $this->entityManager->getRepository($entity);
        foreach ($config as $key => $value) {
            $result = $repository->findBy([
                $key => $value
            ]);
            if (count($result) > 0) {
                return $result;
            }
        }

        return $repository->findBy($config);
    }

    private function makeQuery(string $sql): array
    {
        $dsn = 'pgsql:host=localhost;port=5433;dbname=hospital;user=symfony_user;password=symfony_password';
        $pdo = new PDO($dsn);
        $result = $pdo->query($sql);
        $pdo = null;

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectItemsFromSourceTableById($tableName, $id): array
    {
        $sql = "SELECT * FROM " . $tableName . " where id=" . $id;

        return $this->makeQuery($sql);
    }

    public function getEntityByTypeIsClass($item, $fieldStructure): object|array
    {
        $sourceTable = $fieldStructure['source_table'];
        $sourceSearchId = $item[$fieldStructure["source_fields"][0]];
        $itemFromSource = $this->selectItemsFromSourceTableById($sourceTable, $sourceSearchId);
        if (count($itemFromSource) == 0) {
            ConfigMigrator::$failureCount++;

            return [];
        }
        $valueToSearch = [];
        foreach ($fieldStructure["fields_for_search"] as $key => $value) {
            $valueToSearch[$value] = $itemFromSource[0][$key];
        }
        $findsItem = $this->findItemFromTargetDatabaseByConfig($fieldStructure['type'], $valueToSearch);
        if (count($findsItem) === 0) {
            ConfigMigrator::$failureCount++;

            return [];
        }

        return $findsItem[0];
    }

    public function getIdByTypeIsId($item, $fieldStructure): ?int
    {
        $sourceSearchId = $item[$fieldStructure['source_fields'][0]];
        $sourceTable = $fieldStructure['source_table'];
        $foundItem = $this->selectItemsFromSourceTableById($sourceTable, $sourceSearchId);
        if (count($foundItem) == 0) {
            return null;
        }
        $foundItem = $foundItem[0];
        $fieldForSearchInTargetDatabase = [];
        foreach ($fieldStructure['fields_for_search'] as $key => $value) {
            $fieldForSearchInTargetDatabase[$key] = $foundItem[$value];
        }
        $foundItem = $this->findItemFromTargetDatabaseByConfig($fieldStructure['relation_entity'], $fieldForSearchInTargetDatabase);
        if (count($foundItem) === 0) {
            return null;
        }
        $foundItem = $foundItem[0];

        return $foundItem->getId();
    }

}