<?php

namespace App\Tests\API\Chamber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateApiTest extends WebTestCase
{
    private $client = null;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testUpdateChamber():void
    {
        $client = $this->client;
        $data = [
            "number"=>335
        ];
        $data = json_encode($data);
        $client->request('PATCH', 'http://127.0.0.1:8000/api/chambers/1', server: [
            "CONTENT_TYPE"=>"application/json"
        ], content: $data);

        if($client->getResponse()->getStatusCode()===200){
            $this->assertResponseStatusCodeSame(200);
        }
        else if ($client->getResponse()->getStatusCode()===409){
            $this->assertResponseStatusCodeSame(409);
        }
        $this->assertJson($client->getResponse()->getContent());
    }
    public function testEmptyBodyChamber():void
    {
        $client = $this->client;
        $data = [];
        $data = json_encode($data);
        $client->request('PATCH', 'http://127.0.0.1:8000/api/chambers/1', server: [
            "CONTENT_TYPE"=>"application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());
    }
}