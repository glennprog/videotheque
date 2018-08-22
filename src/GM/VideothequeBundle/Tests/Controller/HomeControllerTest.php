<?php

namespace GM\VideothequeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHome() // testIndex
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Home Videotheque Bundle', $client->getResponse()->getContent());
    }
}
