<?php

namespace App\Tests\API\Chamber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testIndexChambers(): void
    {
       $client = $this->client;
       $client->request('GET','http://127.0.0.1:8000/api/chambers');
       $client->followRedirect();
       $data = json_decode($client->getResponse()->getContent())->data;
        // если у нас нет ничего то мы не проходим тест т.к. нет ничего
        // по хорошему в контроллере бы возвращать статус кодом что данных нет
        if(!$data){
            $this->markTestIncomplete('Data is empty');
        }
       $this->assertResponseIsSuccessful();

       $this->assertJson($client->getResponse()->getContent());
    }


}
