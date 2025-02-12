<?php

namespace App\Tests\API\Patient;


use App\Tests\Factory\PatientFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteApiTest extends WebTestCase
{
    private $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testDeletePatients(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $pf = new PatientFactory($em);
        $patient = $pf->create();

        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/patients/'.$patient->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    public function testDeleteNotValidIdPatients(): void
    {
        $client = $this->client;
        $client->request('DELETE','http://127.0.0.1:8000/api/patients/989888');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());
    }
}
