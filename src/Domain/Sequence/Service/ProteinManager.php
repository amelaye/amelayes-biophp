<?php
/**
 * Protein Managing
 * Inspired by BioPHP's project biophp.org
 * Created 11 february 2019
 * Last modified 18 january 2020
 */
namespace AppBundle\Domain\Sequence\Service;

use AppBundle\Api\Interfaces\AminoApiAdapter;
use AppBundle\Domain\Sequence\Entity\Protein;
use AppBundle\Domain\Sequence\Interfaces\ProteinInterface;

/**
 * We can have manipulation with proteins
 * @author Amélie DUVERNET aka Amelaye <amelieonline@gmail.com>
 * @package AppBundle\Domain\Sequence\Service
 */
class ProteinManager implements ProteinInterface
{
    /**
     * @var Protein
     */
    private $protein;

    /**
     * @var array
     */
    private $wts;

    /**
     * Constructor
     * @param AminoApiAdapter $aminoApi
     */
    public function __construct(AminoApiAdapter $aminoApi)
    {
        $this->wts      = $aminoApi::GetAminoweights($aminoApi->getAminos());
    }

    /**
     * @param $oProtein
     */
    public function setProtein(Protein $oProtein)
    {
        $this->protein = $oProtein;
    }

    /**
     * Returns the length of a protein sequence().
     * @return  int  An integer representing the number of amino acids in the protein.
     */
    public function seqlen() : int
    {
        return strlen($this->protein->getSequence());
    }


    /**
     * Computes the molecular weight of a protein sequence.
     * @return  boolean|array   An array of the form: ( lower_molwt, upper_molwt )
     */
    public function molwt()
    {
        $iLowerLimit = 0;
        $iUpperLimit = 1;

        // Check if characters outside our 20-letter amino alphabet is included in the sequence.
        preg_match_all("/[^GAVLIPFYWSTCMNQDEKRHBXZ]/", $this->protein->getSequence(), $aMatch);

        // If there are unknown characters, then do not compute molwt and instead return FALSE.
        if (count($aMatch[0]) > 0) {
            return false;
        }

        // Otherwise, continue and calculate molecular weight of amino acid chain.
        $aMolecularWeight = [0, 0];
        $iAminoLength = $this->seqlen();

        for($i = 0; $i < $iAminoLength; $i++) {
            $amino = substr($this->protein->getSequence(), $i, 1);
            $aMolecularWeight[$iLowerLimit] += $this->wts[$amino][$iLowerLimit];
            $aMolecularWeight[$iUpperLimit] += $this->wts[$amino][$iUpperLimit];
        }
        $fMwtWater = 18.015;
        $aMolecularWeight[$iLowerLimit] = $aMolecularWeight[$iLowerLimit] - (($this->seqlen() - 1) * $fMwtWater);
        $aMolecularWeight[$iUpperLimit] = $aMolecularWeight[$iUpperLimit] - (($this->seqlen() - 1) * $fMwtWater);
        return $aMolecularWeight;
    }
}