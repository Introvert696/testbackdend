<?php

namespace App\Command\MigratorCommand;

class DataProcessor
{
    public function __construct(
        private readonly DataReader $dataReader
    )
    {
    }

    public function transformSourceItemsToEntity($sourceItems, $structure): array
    {
        $createdItems = [];
        foreach ($sourceItems as $item) {
            $columnForSearch = $this->getSearchParams($structure, $item);
            $duplicates = $this->dataReader->findItemFromTargetDatabaseByConfig($structure['target'], $columnForSearch);
            if (count($duplicates) > 0) {
                ConfigMigrator::$failureCount++;
                continue;
            }
            $createdItem = $this->createEntityItem($structure, $columnForSearch);
            $createdItems[] = $createdItem;
        }

        return $createdItems;
    }

    private function createEntityItem($structure, $columnForSearch): object
    {
        $newItem = new $structure['target']();
        foreach ($structure['fields'] as $field => $value) {
            $setter = $value['setter'];
            $newItem->$setter($columnForSearch[$field]);
        }

        return $newItem;
    }

    public function getSearchParams($structure, $item): array
    {
        $columnForSearch = [];
        foreach ($structure['fields'] as $field => $fieldStructure) {
            $fieldValue = '';
            if (class_exists($fieldStructure['type'])) {
                $sourceTable = $fieldStructure['source_table'];
                $sourceSearchId = $item[$fieldStructure["source_fields"][0]];
                $itemFromSource = $this->dataReader->selectItemsFromSourceTableById($sourceTable, $sourceSearchId);
                if (count($itemFromSource) == 0) {
                    ConfigMigrator::$failureCount++;

                    return [];
                }
                $valueToSearch = [];
                foreach ($fieldStructure["fields_for_search"] as $key => $value) {
                    $valueToSearch[$value] = $itemFromSource[0][$key];
                }
                $findsItem = $this->dataReader->findItemFromTargetDatabaseByConfig($fieldStructure['type'], $valueToSearch);
                if (count($findsItem) === 0) {
                    ConfigMigrator::$failureCount++;

                    return [];
                }
                $fieldValue = $findsItem[0];
            } else if ($fieldStructure['type'] === "id") {
                $sourceSearchId = $item[$fieldStructure['source_fields'][0]];
                $sourceTable = $fieldStructure['source_table'];
                $foundItem = $this->dataReader->selectItemsFromSourceTableById($sourceTable, $sourceSearchId);
                if (count($foundItem) == 0) {
                    return [];
                }
                $foundItem = $foundItem[0];
                $fieldForSearchInTargetDatabase = [];
                foreach ($fieldStructure['fields_for_search'] as $key => $value) {
                    $fieldForSearchInTargetDatabase[$key] = $foundItem[$value];
                }
                $foundItem = $this->dataReader->findItemFromTargetDatabaseByConfig($fieldStructure['relation_entity'], $fieldForSearchInTargetDatabase);
                if (count($foundItem) === 0) {
                    return [];
                }
                $foundItem = $foundItem[0];
                $fieldValue = $foundItem->getId();
            } else if ($fieldStructure['type'] === "default") {
                $fieldValue = $fieldStructure['default'];
            } else {
                foreach ($fieldStructure['source_fields'] as $source_field) {
                    if ($fieldValue !== "") {
                        $fieldValue .= ' ' . $item[$source_field];
                    } else {
                        $fieldValue = $item[$source_field];
                    }
                }
            }
            $columnForSearch[$field] = $fieldValue;
        }

        return $columnForSearch;
    }

}