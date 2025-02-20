<?php

namespace App\Tests\API\Chamber;

use App\Tests\Factory\ChamberFactory;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testDeleteChambers(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $chamberFactory = new ChamberFactory($em);
        $chamber = $chamberFactory->create(1894);

        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/chambers/'.$chamber->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    public function testDeleteNotValidIdChambers(): void
    {
        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/chambers/989888');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }
}
