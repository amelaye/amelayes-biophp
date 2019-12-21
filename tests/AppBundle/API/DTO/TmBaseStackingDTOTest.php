<?php


namespace Tests\AppBundle\API\DTO;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Api\DTO\TmBaseStackingDTO;

class TmBaseStackingDTOTest extends WebTestCase
{
    public function testNewTmBaseStackingDTO()
    {
        $dto = new TmBaseStackingDTO();
        $dto->setId("AA");
        $dto->setTemperatureEnthalpy(-7.9);
        $dto->setTemperatureEnthropy(-22.2);

        $this->assertEquals("AA", $dto->getId());
        $this->assertEquals(-7.9, $dto->getTemperatureEnthalpy());
        $this->assertEquals(-22.2, $dto->getTemperatureEnthropy());
    }
}