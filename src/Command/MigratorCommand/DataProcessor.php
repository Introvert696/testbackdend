<?php

namespace App\Command\MigratorCommand;

class DataProcessor
{
    public function __construct(
        private readonly DataReader $dataReader,
        private readonly DataWriter $dataWriter
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
            $createdItem = $this->dataWriter->createEntityItem($structure, $columnForSearch);
            $createdItems[] = $createdItem;
        }

        return $createdItems;
    }

    public function getSearchParams($structure, $item): array
    {
        $columnForSearch = [];
        foreach ($structure['fields'] as $field => $fieldStructure) {
            $fieldValue = '';
            if (class_exists($fieldStructure['type'])) {
                $fieldValue = $this->dataReader->getEntityByTypeIsClass($item, $fieldStructure);
            } else if ($fieldStructure['type'] === "id") {
                $fieldValue = $this->dataReader->getIdByTypeIsId($item, $fieldStructure);
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