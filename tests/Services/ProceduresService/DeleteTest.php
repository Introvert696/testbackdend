<?php

namespace App\Tests\Services\ProceduresService;

use App\Tests\Services\BaseService;

class DeleteTest extends BaseService
{
    public function testInValidId(): void
    {
        $response = $this->procedureService->delete(9999);
        $this->assertSame($response['code'],404);
        $this->assertSame($response['type'],'Not Found');
    }
    public function testValidId(): void
    {
        $procedureData = [
            "title"=>"test title for delete",
            "description" => "dd ds asd fsa dd dsd"
        ];
        $createdProcedure = $this->procedureService->store(json_encode($procedureData));
        $response = $this->procedureService->delete($createdProcedure['data']->getId());
        $this->assertSame($response['code'],200);
        $this->assertSame($response['type'],'Delete');
    }
}