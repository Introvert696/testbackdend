<?php

namespace App\Command\MigratorCommand;

class DataProcessor
{
    public function __construct(
        private readonly DataReader $dataReader
    )
    {
    }

    public function getColumnFromStructure($tableStructure): array
    {
        $columns = [];
        foreach ($tableStructure as $column => $structure) {
            $columns[] = $column;
        }

        return $columns;
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
            // if field type is entity class
            // need found this item, first in source db
            // next step in target db and get entity item
            if (class_exists($fieldStructure['type'])) {
                // TODO - if type is a class

                $sourceTable = $fieldStructure['source_table'];
                $sourceSearchId = $item[$fieldStructure["source_fields"][0]];
                $itemFromSource = $this->dataReader->selectItemsFromSourceTableById($sourceTable, $sourceSearchId);
                if (count($itemFromSource) == 0) {
                    // if in source db not found items
                    ConfigMigrator::$failureCount++;

                    return [];
                }
                dump($fieldStructure);
                dd($itemFromSource);
                // first get item from source db, because table have a id
                // need search id in source db, and get item with all field,
                // next get field for get value to search in target db,
                // because id not equals in target and source databases
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