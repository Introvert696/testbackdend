<?php

namespace App\Command\MigratorCommand;

use Doctrine\ORM\EntityManagerInterface;

class DataWriter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function saveItems(array $items): void
    {
        foreach ($items as $item) {
            $this->entityManager->persist($item);
            ConfigMigrator::$successCount++;
        }
        $this->entityManager->flush();
    }

    public function createEntityItem($structure, $columnForSearch): object
    {
        $newItem = new $structure['target']();
        foreach ($structure['fields'] as $field => $value) {
            $setter = $value['setter'];
            $newItem->$setter($columnForSearch[$field]);
        }

        return $newItem;
    }

}
