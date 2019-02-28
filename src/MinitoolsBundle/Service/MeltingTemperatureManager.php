<?php
/**
 * MeltingTemperatureManager
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 26 february 2019
 */
namespace MinitoolsBundle\Service;

class MeltingTemperatureManager
{
    /**
     * @param $c
     * @param $conc_primer
     * @param $conc_salt
     * @param $conc_mg
     * @throws \Exception
     */
    public function tmBaseStacking($c, $conc_primer, $conc_salt, $conc_mg)
    {
        try {
            if (CountATCG($c) != strlen($c)) {
                print "The oligonucleotide is not valid";
                return;
            }
            $h = $s = 0;
            // from table at http://www.ncbi.nlm.nih.gov/pmc/articles/PMC19045/table/T2/ (SantaLucia, 1998)
            // enthalpy values
            $array_h["AA"] = -7.9;
            $array_h["AC"] = -8.4;
            $array_h["AG"] = -7.8;
            $array_h["AT"] = -7.2;
            $array_h["CA"] = -8.5;
            $array_h["CC"] = -8.0;
            $array_h["CG"] =-10.6;
            $array_h["CT"] = -7.8;
            $array_h["GA"] = -8.2;
            $array_h["GC"] = -9.8;
            $array_h["GG"] = -8.0;
            $array_h["GT"] = -8.4;
            $array_h["TA"] = -7.2;
            $array_h["TC"] = -8.2;
            $array_h["TG"] = -8.5;
            $array_h["TT"] = -7.9;
            // entropy values
            $array_s["AA"] = -22.2;
            $array_s["AC"] = -22.4;
            $array_s["AG"] = -21.0;
            $array_s["AT"] = -20.4;
            $array_s["CA"] = -22.7;
            $array_s["CC"] = -19.9;
            $array_s["CG"] = -27.2;
            $array_s["CT"] = -21.0;
            $array_s["GA"] = -22.2;
            $array_s["GC"] = -24.4;
            $array_s["GG"] = -19.9;
            $array_s["GT"] = -22.4;
            $array_s["TA"] = -21.3;
            $array_s["TC"] = -22.2;
            $array_s["TG"] = -22.7;
            $array_s["TT"] = -22.2;

            // effect on entropy by salt correction; von Ahsen et al 1999
            // Increase of stability due to presence of Mg;
            $salt_effect = ($conc_salt/1000)+(($conc_mg/1000) * 140);
            // effect on entropy
            $s+=0.368 * (strlen($c)-1)* log($salt_effect);

            // terminal corrections. Santalucia 1998
            $firstnucleotide = substr($c,0,1);
            if($firstnucleotide == "G" || $firstnucleotide == "C") {
                $h += 0.1;
                $s += -2.8;
            }
            if($firstnucleotide == "A" ||  $firstnucleotide == "T") {
                $h += 2.3;
                $s += 4.1;
            }

            $lastnucleotide=substr($c,strlen($c)-1,1);
            if ($lastnucleotide == "G" || $lastnucleotide == "C") {
                $h += 0.1;
                $s += -2.8;
            }
            if ($lastnucleotide == "A" || $lastnucleotide == "T"){
                $h += 2.3;
                $s += 4.1;
            }

            // compute new H and s based on sequence. Santalucia 1998
            for($i=0; $i<strlen($c)-1; $i++) {
                $subc = substr($c,$i,2);
                $h += $array_h[$subc];
                $s += $array_s[$subc];
            }
            $tm = ((1000*$h)/($s+(1.987*log($conc_primer/2000000000))))-273.15;
            print "Tm:                 <font color=880000><b>".round($tm,1)." &deg;C</b></font>";
            print  "\n<font color=008800>  Enthalpy: ".round($h,2)."\n  Entropy:  ".round($s,2)."</font>";
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $primer
     * @throws \Exception
     */
    public function mol_wt($primer){
        try {
            $upper_mwt = $this->molwt($primer,"DNA","upperlimit");
            $lower_mwt = $this->molwt($primer,"DNA","lowerlimit");
            if ($upper_mwt == $lower_mwt) {
                print "Molecular weight:        $upper_mwt";
            } else {
                print "Upper Molecular weight:  $upper_mwt\nLower Molecular weight:  $lower_mwt";
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $c
     * @return int
     * @throws \Exception
     */
    public function countCG($c)
    {
        try {
            $cg = substr_count($c,"G")
                + substr_count($c,"C");
            return $cg;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @note : fonction redondante à refactoriser
     * @param $c
     * @return int
     * @throws \Exception
     */
    public function countATCG($c)
    {
        try {
            $cg = substr_count($c,"A")
                + substr_count($c,"T")
                + substr_count($c,"G")
                + substr_count($c,"C");
            return $cg;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $primer
     * @return float
     * @throws \Exception
     */
    public function tmMin($primer)
    {
        try {
            $primer_len = strlen($primer);
            $primer2 = preg_replace("/A|T|Y|R|W|K|M|D|V|H|B|N/","A",$primer);
            $n_AT = substr_count($primer2,"A");
            $primer2 = preg_replace("/C|G|S/","G",$primer);
            $n_CG = substr_count($primer2,"G");

            if($primer_len > 0) {
                if($primer_len < 14) {
                    return round(2 * ($n_AT) + 4 * ($n_CG));
                } else {
                    return round(64.9 + 41*(($n_CG-16.4)/$primer_len),1);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $primer
     * @return float
     * @throws \Exception
     */
    public function tmMax($primer)
    {
        try {
            $primer_len = strlen($primer);
            $primer = $this->primerMax($primer);
            $n_AT = substr_count($primer,"A");
            $n_CG = substr_count($primer,"G");
            if($primer_len > 0) {
                if($primer_len < 14) {
                    return round(2 * ($n_AT) + 4 * ($n_CG));
                } else {
                    return round(64.9 + 41*(($n_CG-16.4)/$primer_len),1);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $primer
     * @return string|string[]|null
     * @throws \Exception
     */
    public function primerMin($primer)
    {
        try {
            $primer = preg_replace("/A|T|Y|R|W|K|M|D|V|H|B|N/","A",$primer);
            $primer = preg_replace("/C|G|S/","G",$primer);
            return $primer;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $primer
     * @return string|string[]|null
     * @throws \Exception
     */
    function primerMax($primer)
    {
        try {
            $primer = preg_replace("/A|T|W/","A",$primer);
            $primer = preg_replace("/C|G|Y|R|S|K|M|D|V|H|B|N/","G",$primer);
            return $primer;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param $sequence
     * @param $moltype
     * @param $limit
     * @return float|int
     * @throws \Exception
     */
    function molwt($sequence,$moltype,$limit)
    {
        try {
            // the following are single strand molecular weights / base
            $rna_A_wt = 329.245;
            $rna_C_wt = 305.215;
            $rna_G_wt = 345.245;
            $rna_U_wt = 306.195;

            $dna_A_wt = 313.245;
            $dna_C_wt = 289.215;
            $dna_G_wt = 329.245;
            $dna_T_wt = 304.225;

            $water = 18.015;

            $dna_wts = array(
                'A' => array($dna_A_wt, $dna_A_wt),  // Adenine
                'C' => array($dna_C_wt, $dna_C_wt),  // Cytosine
                'G' => array($dna_G_wt, $dna_G_wt),  // Guanine
                'T' => array($dna_T_wt, $dna_T_wt),  // Thymine
                'M' => array($dna_C_wt, $dna_A_wt),  // A or C
                'R' => array($dna_A_wt, $dna_G_wt),  // A or G
                'W' => array($dna_T_wt, $dna_A_wt),  // A or T
                'S' => array($dna_C_wt, $dna_G_wt),  // C or G
                'Y' => array($dna_C_wt, $dna_T_wt),  // C or T
                'K' => array($dna_T_wt, $dna_G_wt),  // G or T
                'V' => array($dna_C_wt, $dna_G_wt),  // A or C or G
                'H' => array($dna_C_wt, $dna_A_wt),  // A or C or T
                'D' => array($dna_T_wt, $dna_G_wt),  // A or G or T
                'B' => array($dna_C_wt, $dna_G_wt),  // C or G or T
                'X' => array($dna_C_wt, $dna_G_wt),  // G, A, T or C
                'N' => array($dna_C_wt, $dna_G_wt)   // G, A, T or C
            );

            $rna_wts = array(
                'A' => array($rna_A_wt, $rna_A_wt),  // Adenine
                'C' => array($rna_C_wt, $rna_C_wt),  // Cytosine
                'G' => array($rna_G_wt, $rna_G_wt),  // Guanine
                'U' => array($rna_U_wt, $rna_U_wt),  // Uracil
                'M' => array($rna_C_wt, $rna_A_wt),  // A or C
                'R' => array($rna_A_wt, $rna_G_wt),  // A or G
                'W' => array($rna_U_wt, $rna_A_wt),  // A or U
                'S' => array($rna_C_wt, $rna_G_wt),  // C or G
                'Y' => array($rna_C_wt, $rna_U_wt),  // C or U
                'K' => array($rna_U_wt, $rna_G_wt),  // G or U
                'V' => array($rna_C_wt, $rna_G_wt),  // A or C or G
                'H' => array($rna_C_wt, $rna_A_wt),  // A or C or U
                'D' => array($rna_U_wt, $rna_G_wt),  // A or G or U
                'B' => array($rna_C_wt, $rna_G_wt),  // C or G or U
                'X' => array($rna_C_wt, $rna_G_wt),  // G, A, U or C
                'N' => array($rna_C_wt, $rna_G_wt)   // G, A, U or C
            );

            $all_na_wts = array('DNA' => $dna_wts, 'RNA' => $rna_wts);
            $na_wts = $all_na_wts[$moltype];

            $mwt = 0;
            $NA_len = strlen($sequence);

            if($limit == "lowerlimit"){
                $wlimit=1;
            }
            if($limit == "upperlimit"){
                $wlimit=0;
            }

            for ($i = 0; $i < $NA_len; $i++) {
                $NA_base = substr($sequence, $i, 1);
                $mwt += $na_wts[$NA_base][$wlimit];
            }
            $mwt += $water;

            return $mwt;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}