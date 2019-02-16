<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSequenceanalysis()
    {
        $client = static::createClient();
        $client->request('GET', '/sequence-analysis');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testParseaseqdb()
    {
        $client = static::createClient();
        $client->request('GET', '/read-sequence-genbank');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
