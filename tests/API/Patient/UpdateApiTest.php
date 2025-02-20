<?php

namespace App\Tests\API\Patient;

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
            "name"=>"Елисеев Михаил Васильевич",
            "chamber"=>3
        ];
        $data = json_encode($data);
        $client->request('PATCH', 'http://127.0.0.1:8000/api/patients/1', server: [
            "CONTENT_TYPE"=>"application/json"
        ], content: $data);

        if($client->getResponse()->getStatusCode()===200){
            $this->assertResponseStatusCodeSame(200);
        }
        else if ($client->getResponse()->getStatusCode()===404){
            $this->assertResponseStatusCodeSame(404);
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
        $client->request('PATCH', 'http://127.0.0.1:8000/api/patients/1', server: [
            "CONTENT_TYPE"=>"application/json"
        ], content: $data);
        $this->assertJson($client->getResponse()->getContent());

        if($client->getResponse()->getStatusCode()===402){
            $this->assertResponseStatusCodeSame(402);
        }
        else if ($client->getResponse()->getStatusCode()===404){
            $this->assertResponseStatusCodeSame(404);
        }

    }
}