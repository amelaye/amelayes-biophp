<?php
/**
 * Oligo-Nucleotids Functions
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 9 march 2019
 * Last modified 22 july 2019
 * RIP Pasha, gone 27 february 2019 =^._.^= ∫
 */
namespace AppBundle\Service;

use AppBundle\Bioapi\Bioapi;

class OligosManager
{
    private $base_a;
    private $base_b;
    private $base_c;
    private $base_d;
    private $base_e;
    private $base_f;
    private $base_g;
    private $base_h;

    private $dnaComplements;

    /**
     * OligosManager constructor.
     * @param Bioapi $bioapi
     */
    public function __construct(Bioapi $bioapi)
    {
        $dnaComplements = $bioapi->getDNAComplement();
        asort($dnaComplements);
        $this->dnaComplements = $dnaComplements;
    }

    /**
     * @return array
     */
    public function getDnaComplements()
    {
        return $this->dnaComplements;
    }

    /**
     * For oligos 2 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos2BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                if(isset($oligos_1step[$val_a.$val_b])) {
                    $oligos[$val_a.$val_b] = $oligos_1step[$val_a.$val_b];
                } else {
                    $oligos[$val_a.$val_b] = 0;
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 3 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos3BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    if(isset($oligos_1step[$val_a.$val_b.$val_c])) {
                        $oligos[$val_a.$val_b.$val_c] = $oligos_1step[$val_a.$val_b.$val_c];
                    } else {
                        $oligos[$val_a.$val_b.$val_c] = 0;
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 4 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos4BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;
        $this->base_d = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        if(isset($oligos_1step[$val_a.$val_b.$val_c.$val_d])) {
                            $oligos[$val_a.$val_b.$val_c.$val_d] = $oligos_1step[$val_a.$val_b.$val_c.$val_d];
                        } else {
                            $oligos[$val_a.$val_b.$val_c.$val_d] = 0;
                        }
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 5 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos5BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;
        $this->base_d = $this->dnaComplements;
        $this->base_e = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        foreach($this->base_e as $key_e => $val_e) {
                            if(isset($oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e])) {
                                $oligos[$val_a.$val_b.$val_c.$val_d.$val_e] = $oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e];
                            } else {
                                $oligos[$val_a.$val_b.$val_c.$val_d.$val_e] = 0;
                            }
                        }
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 6 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos6BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;
        $this->base_d = $this->dnaComplements;
        $this->base_e = $this->dnaComplements;
        $this->base_f = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        foreach($this->base_e as $key_e => $val_e) {
                            foreach($this->base_f as $key_f => $val_f) {
                                if(isset($oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f])) {
                                    $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f] = $oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f];
                                } else {
                                    $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f] = 0;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 7 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos7BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;
        $this->base_d = $this->dnaComplements;
        $this->base_e = $this->dnaComplements;
        $this->base_f = $this->dnaComplements;
        $this->base_g = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        foreach($this->base_e as $key_e => $val_e) {
                            foreach($this->base_f as $key_f => $val_f) {
                                foreach($this->base_g as $key_g => $val_g) {
                                    if(isset($oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g])) {
                                        $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g] = $oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g];
                                    } else {
                                        $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * For oligos 8 bases long
     * @param $oligos_1step
     * @return mixed
     */
    public function findOligos8BasesLong($oligos_1step)
    {
        $this->base_a = $this->dnaComplements;
        $this->base_b = $this->dnaComplements;
        $this->base_c = $this->dnaComplements;
        $this->base_d = $this->dnaComplements;
        $this->base_e = $this->dnaComplements;
        $this->base_f = $this->dnaComplements;
        $this->base_g = $this->dnaComplements;
        $this->base_h = $this->dnaComplements;

        $oligos = [];

        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        foreach($this->base_e as $key_e => $val_e) {
                            foreach($this->base_f as $key_f => $val_f) {
                                foreach($this->base_g as $key_g => $val_g) {
                                    foreach($this->base_h as $key_h => $val_h) {
                                        if(isset($oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g.$val_h])) {
                                            $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g.$val_h] = $oligos_1step[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g.$val_h];
                                        } else {
                                            $oligos[$val_a.$val_b.$val_c.$val_d.$val_e.$val_f.$val_g.$val_h] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $oligos;
    }

    /**
     * Compute frequency of oligonucleotides with length $iOligoLen for sequence $sSequence
     * @param       string      $sSequence
     * @param       int         $iOligoLen
     * @return      array
     * @throws      \Exception
     */
    public function findOligos($sSequence, $iOligoLen)
    {
        try {
            $i              = 0;
            $aOligos1Step   = [];
            $aOligos        = [];

            $iLength = strlen($sSequence) - $iOligoLen + 1;
            while ($i < $iLength) {
                $sMySequence = substr($sSequence, $i, $iOligoLen);

                if (!isset($aOligos1Step[$sMySequence])) {
                    $aOligos1Step[$sMySequence] = 1;
                } else {
                    $aOligos1Step[$sMySequence] ++;
                }
                $i ++;
            }

            switch ($iOligoLen) {
                case 1:
                    foreach($this->dnaComplements as $key => $oligo) {
                        $aOligos[$oligo] = substr_count($sSequence, $oligo);
                    }
                    break;
                case 2:
                    $aOligos = $this->findOligos2BasesLong($aOligos1Step);
                    break;
                case 3:
                    $aOligos = $this->findOligos3BasesLong($aOligos1Step);
                    break;
                case 4:
                    $aOligos = $this->findOligos4BasesLong($aOligos1Step);
                    break;
                case 5:
                    $aOligos = $this->findOligos5BasesLong($aOligos1Step);
                    break;
                case 6:
                    $aOligos = $this->findOligos6BasesLong($aOligos1Step);
                    break;
                case 7:
                    $aOligos = $this->findOligos7BasesLong($aOligos1Step);
                    break;
                case 8:
                    $aOligos = $this->findOligos8BasesLong($aOligos1Step);
                    break;
                default:
                    throwException(new \Exception("Invalid base format ! "));
            }

            return $aOligos;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * COMPUTE Z-SCORES FOR TETRANUCLEOTIDES
     * @param $oligos2
     * @param $oligos3
     * @param $oligos4
     * @return array
     */
    public function findZScore($oligos2, $oligos3, $oligos4)
    {
        $this->base_a = $this->base_b = $this->base_c = $this->base_d = $this->base_e = $this->base_f = $this->dnaComplements;

        $i = 0;
        $zscore = [];
        $exp = [];
        $var = [];
        foreach($this->base_a as $key_a => $val_a) {
            foreach($this->base_b as $key_b => $val_b) {
                foreach($this->base_c as $key_c => $val_c) {
                    foreach($this->base_d as $key_d => $val_d) {
                        if(!isset($oligos3[$val_b.$val_c.$val_d])) {
                            $oligos3[$val_b.$val_c.$val_d] = null;
                        }
                        $atemp = $oligos3[$val_a.$val_b.$val_c] * $oligos3[$val_b.$val_c.$val_d];

                        if(!isset($oligos2[$val_b.$val_c])) {
                            $oligos2[$val_b.$val_c] = null;
                            $exp[$val_a.$val_b.$val_c.$val_d] = 0;
                        } else {
                            $exp[$val_a.$val_b.$val_c.$val_d] = $atemp / $oligos2[$val_b.$val_c];
                        }


                        $btemp = $oligos2[$val_b.$val_c] - $oligos3[$val_b.$val_c.$val_d];
                        $ctemp = $oligos2[$val_b.$val_c] - $oligos3[$val_a.$val_b.$val_c];

                        if(pow($oligos2[$val_b.$val_c],2) != 0) {
                            $dtemp = ($ctemp * $btemp) / pow($oligos2[$val_b.$val_c],2);
                        } else {
                            $dtemp = 0;
                        }

                        $var[$val_a.$val_b.$val_c.$val_d] = $exp[$val_a.$val_b.$val_c.$val_d] * $dtemp;

                        if(!isset($oligos4[$val_a.$val_b.$val_c.$val_d])) {
                            $oligos4[$val_a.$val_b.$val_c.$val_d] = null;
                        }
                        $etemp = $oligos4[$val_a.$val_b.$val_c.$val_d] - $exp[$val_a.$val_b.$val_c.$val_d];

                        if(isset($var[$val_a.$val_b.$val_c.$val_d]) && sqrt($var[$val_a.$val_b.$val_c.$val_d] != 0)) {
                            $zscore[$i] = $etemp / sqrt($var[$val_a.$val_b.$val_c.$val_d]);
                        }
                        $i ++;
                    }
                }
            }
        }

        return $zscore;
    }
}