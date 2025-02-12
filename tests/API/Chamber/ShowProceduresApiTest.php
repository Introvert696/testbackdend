<?php

namespace App\Tests\API\Chamber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowProceduresApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testChambersProcedure(): void
    {
        $client = $this->client;
        $client->request('GET', 'http://127.0.0.1:8000/api/chambers/1/procedures');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    public function testNotFoundChambers(): void
    {
        $client = $this->client;
        $client->request('GET', 'http://127.0.0.1:8000/api/chambers/13423233333/procedures');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }

}
