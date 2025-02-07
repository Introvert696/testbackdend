<?php

namespace App\Tests\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChamberApiTest extends WebTestCase
{
    public function testSomething(): void
    {
       $client = static::createClient();
       $client->request('GET','http://127.0.0.1:8000/api/chambers');
       $client->followRedirect();
       $response = $client->getResponse();
       $responseData = json_decode($response->getContent(),true);

       $this->assertSame(200,$response->getStatusCode());
       $this->assertArrayHasKey('type',$responseData);
       $this->assertArrayHasKey('data',$responseData);

    }
}
