<?php

namespace App\Command\MigratorCommand;

use App\Entity\Chambers;
use App\Entity\ChambersPatients;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;

class ConfigMigrator
{
    // конфигурация для миграции базы данных
    public static array $databaseStructure = [
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
                    'fields_for_search' => [
                        'number' => "ward_number"
                    ],
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
    public static int $successCount = 0;
    public static int $failureCount = 0;

    public static function resetCounts(): void
    {
        self::$successCount = 0;
        self::$failureCount = 0;
    }

}