<?php
/**
 * Entity used by form ProteinPropertiesType
 * @author Amélie DUVERNET akka Amelaye
 * Freely inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 26 february 2019
 */
namespace MinitoolsBundle\Entity;


class Protein
{
    private $seq;

    private $start;

    private $end;

    private $composition;

    private $molweight;

    private $abscoef;

    private $charge;

    private $dataSource;

    private $charge2;

    private $pH;

    private $threeLetters;

    private $type1;

    private $type2;


    public function getSeq()
    {
        return $this->seq;
    }

    public function setSeq($seq)
    {
        $this->seq = $seq;
    }

    public function getStart()
    {
        return $this->start;
    }
    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getEnd()
    {
        return $this->end;
    }
    public function setEnd($end){
        $this->end = $end;
    }

    public function getComposition()
    {
        return $this->composition;
    }
    public function setComposition($composition)
    {
        $this->composition = $composition;
    }

    public function getMolweight()
    {
        return $this->molweight;
    }
    public function setMolweight($molweight)
    {
        $this->molweight = $molweight;
    }

    public function getAbscoef()
    {
        return $this->abscoef;
    }
    public function setAbscoef($abscoef)
    {
        $this->abscoef = $abscoef;
    }

    public function getCharge()
    {
        return $this->charge;
    }
    public function setCharge($charge)
    {
        $this->charge = $charge;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getCharge2()
    {
        return $this->charge2;
    }
    public function setCharge2($charge2)
    {
        $this->charge2 = $charge2;
    }

    public function getPH()
    {
        return $this->pH;
    }
    public function setPH($pH)
    {
        $this->pH = $pH;
    }

    public function getThreeLetters()
    {
        $this->threeLetters;
    }
    public function setThreeLetters($threeLetters)
    {
        $this->threeLetters = $threeLetters;
    }

    public function getType1()
    {
        return $this->type1;
    }
    public function setType($type1)
    {
        $this->type1 = $type1;
    }

    public function getType2()
    {
        return $this->type2;
    }
    public function setType2($type2)
    {
        $this->type2 = $type2;
    }
}