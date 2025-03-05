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
        // create find by one field and all
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


}