<?php

namespace App\Tests\API\Chamber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateProcedureApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testSuccessfulUpdate(): void
    {
        $client = $this->client;
        $data = [
            [
                "procedure_id" => 8,
                "queue" => 3,
                "status" => true
            ],
            [
                "procedure_id" => 6,
                "queue" => 2,
                "status" => false
            ],
        ];
        $data = json_encode($data);
        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/4/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testWithoutOneFiled(): void
    {
        $client = $this->client;
        $data = [
            [
                "procedure_id" => 5,
                "queue" => 3
            ],
            [
                "procedure_id" => 6,
                "status" => false
            ],
        ];
        $data = json_encode($data);
        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/1/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(402);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testEmptyBody(): void
    {
        $client = $this->client;
        $data = [

        ];
        $data = json_encode($data);
        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/1/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(402);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testNotValidBody(): void
    {
        $client = $this->client;
        // body с ошибкой
        $data = "[{'procedure_id':5,'queue':3,'status':'tocedure_id':6,'queue':2,'status':false}]";

        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/1/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(402);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testNotFoundChamber(): void
    {
        $client = $this->client;
        $data = [
            [
                "procedure_id" => 5,
                "queue" => 3,
                "status" => true
            ],
            [
                "procedure_id" => 6,
                "queue" => 2,
                "status" => false
            ],
        ];
        $data = json_encode($data);
        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/132123/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testNoExistsProcedure(): void
    {
        $client = $this->client;
        $data = [
            [
                "procedure_id" => 522232,
                "queue" => 3,
                "status" => true
            ],
            [
                "procedure_id" => 657566,
                "queue" => 2,
                "status" => false
            ],
        ];
        $data = json_encode($data);
        $client->request('POST', 'http://127.0.0.1:8000/api/chambers/1/procedures', server: [
            "CONTENT_TYPE" => "application/json"
        ], content: $data);

        $this->assertResponseStatusCodeSame(402);
        $this->assertJson($client->getResponse()->getContent());
    }

}
