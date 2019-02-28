<?php
/**
 * Restrictions Digest  Functions
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 27 february 2019 - RIP Pasha =^._.^= ∫
 */
namespace MinitoolsBundle\Service;

class RestrictionDigestManager
{
    /**
     * RestrictionDigestManager constructor.
     * @param $aEnzymes
     */
    public function __construct($aEnzymes)
    {
        $this->aEnzymes = $aEnzymes;
    }

    /**
     * @param $enzymes_array
     * @param $minimun
     * @param $retype
     * @param $defined_sq
     * @param $wre
     * @return mixed
     */
    public function reduceEnzymesArray($enzymes_array, $minimun, $retype, $defined_sq, $wre)
    {
        // if $wre => all endonucleases but the selected one must be removed
        if($wre) {
            foreach($enzymes_array as $key => $val) {
                if (strpos(" ,".$enzymes_array[$key][0].",",$wre)>0){
                    $new_array[$wre] = $enzymes_array[$key];
                    return $new_array;
                }
            }
        }
        // remove endonucleases which do not match requeriments
        foreach($enzymes_array as $enzyme => $val) {
            // if retype==1 -> only Blund ends (continue for rest)
            if ($retype == 1 && $enzymes_array[$enzyme][5] != 0) {
                continue;
            }
            // if retype==2 -> only Overhang end (continue for rest)
            if ($retype==2 && $enzymes_array[$enzyme][5] == 0) {
                continue;
            }
            // Only endonucleases with which recognized in template a minimum of bases (continue for rest)
            if ($minimun > $enzymes_array[$enzyme][6]) {
                continue;
            }
            // if defined sequence selected, no N (".") or "|" in pattern
            if ($defined_sq == 1) {
                if (strpos($enzymes_array[$enzyme][2],".") >0 || strpos($enzymes_array[$enzyme][2],"|")>0){
                    continue;
                }
            }
            $enzymes_array2[$enzyme] = $enzymes_array[$enzyme];
        }
        return $enzymes_array2;
    }


    /**
     * Calculate digestion results - will return an array like this
     * $digestion[$enzyme]["cuts"] - with number of cuts within the sequence
     * @param $enzymes_array
     * @param $sequence
     * @return mixed
     */
    public function restrictionDigest($enzymes_array, $sequence)
    {
        foreach ($enzymes_array as $enzyme => $val) {
            // this is to put together results for IIb endonucleases, which are computed as "enzyme_name" and "enzyme_name@"
            $enzyme2 = str_replace("@","",$enzyme);

            // split sequence based on pattern from restriction enzyme
            $fragments = preg_split("/".$enzymes_array[$enzyme][2]."/", $sequence,-1,PREG_SPLIT_DELIM_CAPTURE);
            reset ($fragments);
            $maxfragments = sizeof($fragments);
            // when sequence is cleaved ($maxfragments>1) start further calculations
            if($maxfragments > 1) {
                $recognitionposition = strlen($fragments[0]);
                $counter_cleavages = 0;
                $list_of_cleavages = "";
                // for each frament generated, calculate cleavage position,
                // add it to a list, and add 1 to counter
                for($i = 2; $i < $maxfragments; $i += 2) {
                    $cleavageposition = $recognitionposition + $enzymes_array[$enzyme][4];
                    $digestion[$enzyme2]["cuts"][$cleavageposition] = "";
                    // As overlapping may occur for many endonucleases,
                    //   a subsequence starting in position 2 of fragment is calculate
                    $subsequence = substr($fragments[$i-1],1)
                        .$fragments[$i]
                        .substr($fragments[$i+1],0,40);
                    $subsequence = substr($subsequence,0,2 * $enzymes_array[$enzyme][3] - 2);
                    //Previous process is repeated
                    // split subsequence based on pattern from restriction enzyme
                    $fragments_subsequence = preg_split($enzymes_array[$enzyme][2],$subsequence);
                    // when subsequence is cleaved start further calculations
                    if(sizeof($fragments_subsequence) > 1) {
                        // for each fragment of subsequence, calculate overlapping cleavage position,
                        //    add it to a list, and add 1 to counter
                        $overlapped_cleavage = $recognitionposition + 1 + strlen($fragments_subsequence[0]) + $enzymes_array[$enzyme][4];
                        $digestion[$enzyme2]["cuts"][$overlapped_cleavage]="";
                    }
                    // this is a counter for position
                    $recognitionposition += strlen($fragments[$i-1]) + strlen($fragments[$i]);
                }
            }
        }
        return $digestion;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function extractSequences($text)
    {
        if (substr_count($text,">") == 0) {
            $sequence[0]["seq"] = preg_replace("/\W|\d/", "", strtoupper ($text));
        } else {
            $arraysequences = preg_split("/>/", $text,-1,PREG_SPLIT_NO_EMPTY);
            $counter = 0;
            foreach($arraysequences as $key => $val) {
                $seq = substr($val,strpos($val,"\n"));
                $seq = preg_replace ("/\W|\d/", "", strtoupper($seq));
                if (strlen($seq)>0){
                    $sequence[$counter]["seq"] = $seq;
                    $sequence[$counter]["name"] = substr($val,0,strpos($val,"\n"));
                    $counter++;
                }
            }
        }
        return $sequence;
    }


    /**
     * this array includes all endonucleases related information required in this script
     * All enzymes with the same recognition pattern are grouped
     * @return mixed
     */
    public function getArrayOfTypeIIEndonucleases()
    {
        $enzymes_array = $this->aEnzymes["typeII_endonucleolases"];
        return $enzymes_array;
    }


    /**
     * @return mixed
     */
    public function getArrayOfTypeIIsEndonucleases()
    {
        $enzymes_array = $this->aEnzymes["typeIIs_endonucleolases"];
        return $enzymes_array;
    }


    /**
     * @return mixed
     */
    public function getArrayOfTypeIIbEndonucleases()
    {
        $enzymes_array = $this->aEnzymes["type_IIb_endonucleases"];
        return $enzymes_array;
    }


    /**
     * This function return the list of sellers for all endonucleases included in this script
     * @return array
     */
    public function endonucleaseVendors()
    {
        $vendors = $this->aEnzymes["vendors"];
        return $vendors;
    }


    /**
     * @param $company
     * @param $enzyme
     * @return string
     * @todo better integrate in twig
     */
    public function showVendors($company, $enzyme)
    {
        $company = " ".$company;
        $sMessage = '<b>'.$enzyme.'</b><a href="http://rebase.neb.com/rebase/enz/'.$enzyme.'.html">REBASE</a>\n<pre>';
        if(strpos($company,"C") > 0) {
            $sMessage .= ' <a href="http://www.minotech.gr">Minotech Biotechnology</a>\n';
        }
        if(strpos($company,"E") > 0) {
            $sMessage .= ' <a href="http://www.stratagene.com">Stratagene</a>\n';
        }
        if(strpos($company,"F") > 0) {
            $sMessage .= ' <a href="http://www.fermentas.com/catalog/re/'.$re.'.htm">Fermentas AB</a>\n';
        }
        if(strpos($company,"H") > 0) {
            $sMessage .= ' <a href="http://www.aablabs.com/">American Allied Biochemical, Inc.</a>\n';
        }
        if(strpos($company,"I") > 0) {
            $sMessage .= ' <a href="http://www.sibenzyme.com">SibEnzyme Ltd.</a>\n';
        }
        if(strpos($company,"J") > 0) {
            $sMessage .= ' <a href="http://www.nippongene.jp/">Nippon Gene Co., Ltd.</a>\n';
        }
        if(strpos($company,"K") > 0) {
            $sMessage .= ' <a href="http://www.takarashuzo.co.jp/english/index.htm">Takara Shuzo Co. Ltd.</a>\n';
        }
        if(strpos($company,"M") > 0) {
            $sMessage .= ' <a href="http://www.roche.com">Roche Applied Science</a>\n';
        }
        if(strpos($company,"N") > 0) {
            $sMessage .= ' <a href="http://www.neb.com">New England Biolabs</a>\n';
        }
        if(strpos($company,"O") > 0) {
            $sMessage .= ' <a href="http://www.toyobo.co.jp/e/">Toyobo Biochemicals</a>\n';
        }
        if(strpos($company,"P") > 0) {
            $sMessage .= ' <a href="http://www.cvienzymes.com/">Megabase Research Products</a>\n';
        }
        if(strpos($company,"Q") > 0) {
            $sMessage .= ' <a href="http://www.CHIMERx.com">CHIMERx</a>\n';
        }
        if(strpos($company,"R") > 0) {
            $sMessage .= ' <a href="http://www.promega.com">Promega Corporation</a>\n';
        }
        if(strpos($company,"S") > 0) {
            $sMessage .= ' <a href="http://www.sigmaaldrich.com/">Sigma Chemical Corporation</a>n\n';
        }
        if(strpos($company,"U") > 0) {
            $sMessage .= ' <a href="http://www.bangaloregenei.com/">Bangalore Genei</a>\n';
        }
        if(strpos($company,"V") > 0) {
            $sMessage .= ' <a href="http://www.mrc-holland.com">MRC-Holland</a>\n';
        }
        if(strpos($company,"X") > 0) {
            $sMessage .= ' <a href="http://www.eurx.com.pl/index.php?op=catalog&cat=8">EURx Ltd.</a>\n';
        }
        $sMessage .= "</pre>";
        return $sMessage;
    }
}