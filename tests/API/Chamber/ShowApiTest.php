<?php

namespace App\Tests\API\Chamber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testExistsChambers(): void
    {
        $client = $this->client;
        $client->request('GET', 'http://127.0.0.1:8000/api/chambers/' . '2');
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testNotExistsChambers(): void
    {
        $client = $this->client;
        $client->request('GET', 'http://127.0.0.1:8000/api/chambers/99933999');
        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }

}
