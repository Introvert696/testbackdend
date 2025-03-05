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

    public function saveItems(array $items)
    {
        foreach ($items as $item) {
            $this->entityManager->persist($item);
            ConfigMigrator::$successCount++;
        }
        $this->entityManager->flush();
    }

}
