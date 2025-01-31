<?php

namespace App\Services;

use App\Entity\ProcedureList;
use App\Entity\Procedures;

class ProcedureListService
{
    public function createObject(Procedures $proc,int $queue,int $sourceId,bool $status=false) :ProcedureList
    {
        $procedureList = new ProcedureList();
        $procedureList->setProcedures($proc);
        $procedureList->setQueue($queue);
        $procedureList->setSourceType("chambers");
        $procedureList->setSourceId($sourceId);
        $procedureList->setStatus(false);
        return $procedureList;
    }
}