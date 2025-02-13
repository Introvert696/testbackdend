<?php

namespace App\Tests\API\Procedure;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testShowPatientApi(): void
    {
        $client = $this->client;
        $client->request('GET','http://127.0.0.1:8000/api/procedures/1');
        $this->assertResponseIsSuccessful();

        $this->assertJson($client->getResponse()->getContent());
    }
    public function testNotFoundShowPatientApi(): void
    {
        $client = $this->client;
        $client->request('GET','http://127.0.0.1:8000/api/procedures/1324232');
        $this->assertResponseStatusCodeSame(404);

        $this->assertJson($client->getResponse()->getContent());
    }
}