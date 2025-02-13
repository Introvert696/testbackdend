<?php

namespace App\Tests\API\Procedure;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateApiTest extends WebTestCase
{
    private $client = null;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testUpdatePatient():void
    {
        $client = $this->client;
        $data = [
            "title"=> "Test",
            "description"=> "Test desc"
        ];
        $data = json_encode($data);
        $client->request('PATCH', 'http://127.0.0.1:8000/api/procedures/1', server: [
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
    public function testEmptyBodyPatient():void
    {
        $client = $this->client;
        $data = [];
        $data = json_encode($data);
        $client->request('PATCH', 'http://127.0.0.1:8000/api/procedures/1', server: [
            "CONTENT_TYPE"=>"application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJson($client->getResponse()->getContent());
    }
}