<?php
namespace Tests\Domain\Database\Entity;

use Amelaye\BioPHP\Domain\Database\Entity\Collection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CollectionTest extends WebTestCase
{
    public function testNewCollection()
    {
        $collection = new Collection();
        $collection->setId(1);
        $collection->setNomCollection("humandb");

        $this->assertEquals(1, $collection->getId());
        $this->assertEquals("humandb", $collection->getNomCollection());
    }
}