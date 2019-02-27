<?php
/**
 * proteins properties Functions
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 24 february 2019
 * Last modified 24 february 2019
 */
namespace MinitoolsBundle\Service;

class ProteinPropertiesManager
{
    private $pkValues;
    private $aCodons;

    public function __construction($pkValues, $aCodons)
    {
        $this->pkValues = $pkValues;
        $this->aCodons = $aCodons;
    }

    /**
     * @param $seq
     * @return string
     * @throws \Exception
     */
    public function removeNonCodingProt($seq)
    {
        try {
            // change the sequence to upper case
            $seq = strtoupper($seq);
            // remove non-coding characters([^ARNDCEQGHILKMFPSTWYVX\*])
            $seq = preg_replace("([^ARNDCEQGHILKMFPSTWYVX\*])", "", $seq);
            return $seq;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * At isoelectric point, charge of protein will be
     * To calculate pH where charge is 0 a loop is required
     * The loop will start computing charge of protein at pH=7, and if charge is not 0, new charge value will be computed
     * by using a different pH. Procedure will be repeated until charge is 0 (at isoelectric point)
     * @param $pK
     * @param $aminoacid_content
     * @return float
     * @throws \Exception
     */
    public function proteinIsoelectricPoint($pK, $aminoacid_content)
    {
        try {
            $pH = 7;          // pH value at start
            $delta = 4;       // this parameter will be used to modify pH when charge!=0. The value of $delta will change during the loop
            while(1) {
                // compute charge of protein at corresponding pH (uses a function)
                $charge = $this->proteinCharge($pK, $aminoacid_content, $pH);
                // check whether $charge is 0 (consecuentely, pH will be the isoelectric point
                if (round($charge,4) == 0) {
                    break;
                }
                // next line to check how computation is perform
                // print "<br>$charge\t$pH";
                // modify pH for next round
                if ($charge > 0) {
                    $pH = $pH + $delta;
                } else {
                    $pH = $pH - $delta;
                }
                // reduce value for $delta
                $delta = $delta/2;
            }
            // return pH at which charge=0 (the isoelectric point) with two decimals
            return round($pH,2);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $val1
     * @param $val2
     * @return float|int
     * @throws \Exception
     */
    public function partialCharge($val1,$val2)
    {
        try {
            // compute concentration ratio
            $cr = pow(10,$val1-$val2);
            // compute partial charge
            $pc = $cr/($cr+1);
            return $pc;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Computes protein charge at corresponding pH
     * @param $pK
     * @param $aminoacid_content
     * @param $pH
     * @return float|int
     * @throws \Exception
     */
    function proteinCharge($pK,$aminoacid_content,$pH)
    {
        try {
            $charge = $this->partialCharge($pK["N_terminus"], $pH);
            $charge+= $this->partialCharge($pK["K"], $pH) * $aminoacid_content["K"];
            $charge+= $this->partialCharge($pK["R"], $pH) * $aminoacid_content["R"];
            $charge+= $this->partialCharge($pK["H"], $pH) * $aminoacid_content["H"];
            $charge-= $this->partialCharge($pH, $pK["D"]) * $aminoacid_content["D"];
            $charge-= $this->partialCharge($pH, $pK["E"]) * $aminoacid_content["E"];
            $charge-= $this->partialCharge($pH, $pK["C"]) * $aminoacid_content["C"];
            $charge-= $this->partialCharge($pH, $pK["Y"]) * $aminoacid_content["Y"];
            $charge-= $this->partialCharge($pH, $pK["C_terminus"]);
            return $charge;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * pK values for each component (aa)
     * @param $data_source
     * @return array
     * @throws \Exception
     */
    public function pKValues($data_source)
    {
        try {
            return $this->pkValues[$data_source];
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $aminoacid_content
     * @return string
     * @throws \Exception
     */
    public function printAminoacidContent($aminoacid_content)
    {
        try {
            $results = "";
            foreach($aminoacid_content as $aa => $count) {
                $results .= "$aa\t".$this->seq1letterTo3letter($aa)."\t$count\n";
            }
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $seq
     * @return array
     * @throws \Exception
     */
    function aminoacidContent($seq)
    {
        try {
            $array = array(
                "A" => 0,
                "R" => 0,
                "N" => 0,
                "D" => 0,
                "C" => 0,
                "E" => 0,
                "Q" => 0,
                "G" => 0,
                "H" => 0,
                "I" => 0,
                "L" => 0,
                "K" => 0,
                "M" => 0,
                "F" => 0,
                "P" => 0,
                "S" => 0,
                "T" => 0,
                "W" => 0,
                "Y" => 0,
                "V" => 0,
                "X" => 0,
                "*" => 0
            );
            for($i = 0; $i < strlen($seq); $i++){
                $aa = substr($seq, $i,1);
                $array[$aa]++;
            }
            return $array;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Prediction of the molar absorption coefficient of a protein
     * Pace et al. . Protein Sci. 1995;4:2411-23.
     * @param $seq
     * @param $aminoacid_content
     * @param $molweight
     * @return float|int
     * @throws \Exception
     */
    public function molarAbsorptionCoefficientOfProt($seq,$aminoacid_content,$molweight)
    {
        try {
            $abscoef = ($aminoacid_content["A"]*5500 + $aminoacid_content["Y"]*1490 + $aminoacid_content["C"]*125)/$molweight;
            return $abscoef;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Molecular weight calculation
     * @param $seq
     * @param $aminoacid_content
     * @return float
     * @throws \Exception
     */
    public function proteinMolecularWeight ($seq, $aminoacid_content)
    {
        try {
            $molweight  = $aminoacid_content["A"] * 71.07;         // for Alanine
            $molweight += $aminoacid_content["R"] * 156.18;        // for Arginine
            $molweight += $aminoacid_content["N"] * 114.08;        // for Asparagine
            $molweight += $aminoacid_content["D"] * 115.08;        // for Aspartic Acid
            $molweight += $aminoacid_content["C"] * 103.10;        // for Cysteine
            $molweight += $aminoacid_content["Q"] * 128.13;        // for Glutamine
            $molweight += $aminoacid_content["E"] * 129.11;        // for Glutamic Acid
            $molweight += $aminoacid_content["G"] * 57.05;         // for Glycine
            $molweight += $aminoacid_content["H"] * 137.14;        // for Histidine
            $molweight += $aminoacid_content["I"] * 113.15;        // for Isoleucine
            $molweight += $aminoacid_content["L"] * 113.15;        // for Leucine
            $molweight += $aminoacid_content["K"] * 128.17;        // for Lysine
            $molweight += $aminoacid_content["M"] * 131.19;        // for Methionine
            $molweight += $aminoacid_content["F"] * 147.17;        // for Phenylalanine
            $molweight += $aminoacid_content["P"] * 97.11;         // for Proline
            $molweight += $aminoacid_content["S"] * 87.07;         // for Serine
            $molweight += $aminoacid_content["T"] * 101.10;        // for Threonine
            $molweight += $aminoacid_content["W"] * 186.20;        // for Tryptophan
            $molweight += $aminoacid_content["Y"] * 163.17;        // for Tyrosine
            $molweight += $aminoacid_content["Z"] * 99.13;         // for Valine
            $molweight += 18.02;                     // water
            $molweight += $aminoacid_content["X"] * 114.822;       // for unkwon aminoacids, add avarage of all aminoacids
            return $molweight;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $aa
     * @return int|string
     * @throws \Exception
     */
    public function identifyAminoacidCompleteName($aa)
    {
        try {
            $aa = strtoupper($aa);

            foreach($this->aCodons as $name => $codons) {
                if (strlen($aa) == 1) {
                    if ($aa == $codons[1]) {
                        return $name;
                    }
                } elseif (strlen($aa) == 3) {
                    if (isset($codons[3]) && $aa == strtoupper($codons[3])) {
                        return $name;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $seq
     * @return string|string[]|null
     * @throws \Exception
     */
    public function seq1letterTo3letter($seq)
    {
        try {
            $seq = chunk_split($seq,1,'#');
            $seq = chunk_split($seq, 40);
            for($i=0; $i<strlen($seq); $i++){
                $seq = preg_replace("(A\#)","Ala", $seq);
                $seq = preg_replace("(R\#)","Arg", $seq);
                $seq = preg_replace("(N\#)","Asp", $seq);
                $seq = preg_replace("(D\#)","Asn", $seq);
                $seq = preg_replace("(C\#)","Cys", $seq);
                $seq = preg_replace("(E\#)","Glu", $seq);
                $seq = preg_replace("(Q\#)","Gln", $seq);
                $seq = preg_replace("(G\#)","Gly", $seq);
                $seq = preg_replace("(H\#)","His", $seq);
                $seq = preg_replace("(I\#)","Ile", $seq);
                $seq = preg_replace("(L\#)","Leu", $seq);
                $seq = preg_replace("(K\#)","Lys", $seq);
                $seq = preg_replace("(M\#)","Met", $seq);
                $seq = preg_replace("(F\#)","Phe", $seq);
                $seq = preg_replace("(P\#)","Pro", $seq);
                $seq = preg_replace("(S\#)","Ser", $seq);
                $seq = preg_replace("(T\#)","Thr", $seq);
                $seq = preg_replace("(W\#)","Trp", $seq);
                $seq = preg_replace("(Y\#)","Tyr", $seq);
                $seq = preg_replace("(V\#)","Val", $seq);
                $seq = preg_replace("(X\#)","XXX", $seq);
                $seq = preg_replace("(\*\#)","*** ", $seq);
            }
            return $seq;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    public function proteinAminoacidNature1($seq)
    {
        try {
            $result = "";
            for($i = 0; $i < strlen($seq); $i++) {
                // non-polar aminoacids, magenta
                if (strpos(" GAPVILFM", substr($seq,$i,1)) > 0) {
                    $result .= "<font color=yellow>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // polar aminoacids, magenta
                if (strpos(" SCTNQHYW", substr($seq,$i,1)) > 0) {
                    $result .= "<font color=magenta>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // charged aminoacids, red
                if (strpos(" DEKR", substr($seq,$i,1)) > 0) {
                    $result.="<font color=red>".substr($seq,$i,1)."</font>";
                    continue;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $seq
     * @return string
     * @throws \Exception
     */
    public function proteinAminoacidNature2($seq)
    {
        try {
            $result="";
            for($i = 0; $i < strlen($seq); $i++) {
                // Small nonpolar (yellow)
                if (strpos(" GAST",substr($seq,$i,1)) > 0) {
                    $result .= "<font color=yellow>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // Small hydrophobic (green)
                if (strpos(" CVILPFYMW",substr($seq,$i,1)) > 0) {
                    $result .= "<font color=green>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // Polar
                if (strpos(" DQH",substr($seq,$i,1)) > 0) {
                    $result.="<font color=magenta>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // Negatively charged
                if (strpos(" NE",substr($seq,$i,1)) > 0) {
                    $result.="<font color=red>".substr($seq,$i,1)."</font>";
                    continue;
                }
                // Positively charged
                if (strpos(" KR",substr($seq,$i,1)) > 0) {
                    $result.="<font color=red>".substr($seq,$i,1)."</font>";
                    continue;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Chemical group/aminoacids:
     * L/GAVLI       Amino Acids with Aliphatic R-Groups
     * H/ST          Non-Aromatic Amino Acids with Hydroxyl R-Groups
     * M/NQ          Acidic Amino Acids
     * R/FYW         Amino Acids with Aromatic Rings
     * S/CM          Amino Acids with Sulfur-Containing R-Groups
     * I/P           Imino Acids
     * A/DE          Acidic Amino Acids
     * C/KRH         Basic Amino Acids
     * $ X/X
     * @param $amino_seq
     * @return string
     * @throws \Exception
     */
    public function proteinAminoacidsChemicalGroup($amino_seq)
    {
        try {
            $chemgrp_seq = "";
            $ctr = 0;
            while(1) {
                $amino_letter = substr($amino_seq, $ctr, 1);
                if ($amino_letter == "") {
                    break;
                }
                if (strpos(" GAVLI", $amino_letter) > 0) {
                    $chemgrp_seq .= "L";
                }
                elseif (($amino_letter == "S") || ($amino_letter == "T")) {
                    $chemgrp_seq .= "H";
                }
                elseif (($amino_letter == "N") || ($amino_letter == "Q")) {
                    $chemgrp_seq .= "M";
                }
                elseif (strpos(" FYW", $amino_letter)>0) {
                    $chemgrp_seq .= "R";
                }
                elseif (($amino_letter == "C") || ($amino_letter == "M")) {
                    $chemgrp_seq .= "S";
                }
                elseif ($amino_letter == "P") {
                    $chemgrp_seq .= "I";
                }
                elseif (($amino_letter == "D") || ($amino_letter == "E")) {
                    $chemgrp_seq .= "A";
                }
                elseif (($amino_letter == "K") || ($amino_letter == "R") || ($amino_letter == "H")) {
                    $chemgrp_seq .= "C";
                }
                elseif ($amino_letter == "*") {
                    $chemgrp_seq .= "*";
                }
                elseif ($amino_letter == "X" or $amino_letter == "N") {
                    $chemgrp_seq .= "X";
                }
                else {
                    die("Invalid amino acid symbol in input sequence.");
                }
                $ctr++;
            }
            return $chemgrp_seq;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}