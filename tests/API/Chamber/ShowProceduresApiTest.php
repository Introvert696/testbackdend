<?php

namespace App\Tests\API\Chamber;

use App\Entity\Chambers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowProceduresApiTest extends WebTestCase
{
    private $client;
    private $container;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->container= static::getContainer();
    }
    public function testChambersProcedure(): void
    {
        $client = $this->client;
        $client->request('GET', 'http://127.0.0.1:8000/api/chambers/5/procedures');

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
