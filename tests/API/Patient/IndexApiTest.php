<?php

namespace App\Tests\API\Patient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testIndexPatients(): void
    {
        $client = $this->client;
        $client->request('GET','http://127.0.0.1:8000/api/patients');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertJson($client->getResponse()->getContent());
    }
}
