<?php
/**
 * Traits for oligos formatting
 * Freely inspired by BioPHP's project biophp.org
 * Created 29 june 2019
 * Last modified 29 june 2019
 */
namespace AppBundle\Traits;

/**
 * Trait OligoTrait
 * @package AppBundle\Traits
 * @author Amélie DUVERNET akka Amelaye <amelieonline@gmail.com>
 */
trait OligoTrait
{
    /**
     * Place sequence and reverse complement of sequence in one line
     * @param $sSequence
     * @param $dnaComplements
     */
    public function createInversion(&$sSequence, $dnaComplements)
    {
        $seqRevert = strrev($sSequence);
        foreach ($dnaComplements as $nucleotide => $complement) {
            $seqRevert = str_replace($nucleotide, strtolower($complement), $seqRevert);
        }
        $sSequence .= " ".strtoupper($seqRevert);
    }
}
