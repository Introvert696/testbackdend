<?php

namespace App\Tests\API\Procedure;

use App\Tests\Factory\ProcedureFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteApiTest extends WebTestCase
{
    private $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testDeletePatients(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $pr = new ProcedureFactory($em);
        $procedures = $pr->create("322");
        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/procedures/'.$procedures->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    public function testDeleteNotValidIdPatients(): void
    {
        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/procedures/989888');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }
}
