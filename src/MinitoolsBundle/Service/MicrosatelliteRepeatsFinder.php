<?php
/**
 * Microsatellite Repeats Finder Fnctions
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 26 february 2019
 */
namespace MinitoolsBundle\Service;

class MicrosatelliteRepeatsFinder
{
    /**
     * This function will search for microsatellite repeats within a sequence. A microsatellite repeat is defined as a sequence
     * which shows a repeated pattern, as for example in sequence 'ACGTACGTACGTACGT', where 'ACGT' is repeated
     * 4 times. The function allows searching for this kind of subsequences within a sequence.
     * so that sequence AACCGGTT-AAGCGGTT-AACCGGAT-AACCGGTT may be considered as a microsatellite repeat
     * @param   $sequence               is the sequence
     * @param   $min_length             are the range of oligo lengths to be searched; p.e. oligos with length 2 to 6
     * @param   $max_length             are the range of oligo lengths to be searched; p.e. oligos with length 2 to 6
     * @param   $min_repeats            minimal number of time a sequence must be repeated to be considered as a microsatellite repeat
     * @param   $min_length_of_MR       minimum length of tanden repeat; to avoid considering AAAA as a microsatellite repeat, set it to >4
     * @param   $mismatches_allowed     the porcentage of errors allowed when searching in the repetitive sequence
     * @return  array
     * @throws \Exception
     */
    public function findMicrosatelliteRepeats($sequence,$min_length,$max_length,$min_repeats,$min_length_of_MR,$mismatches_allowed)
    {
        try {
            $len_seq = strlen($sequence);
            $counter = 0;
            for ($i = 0; $i < $len_seq-3; $i++) {
                for($j = $min_length; $j < $max_length+1; $j++) {
                    if(($i+$j) > $len_seq) {
                        break;
                    }
                    $sub_seq = substr($sequence,$i,$j);
                    $len_sub_seq = strlen($sub_seq);
                    $mismatches = floor($len_sub_seq * $mismatches_allowed / 100);
                    if ($mismatches == 1) {
                        $sub_seq_pattern = $this->includeN1($sub_seq,0);
                    }
                    elseif ($mismatches == 2) {
                        $sub_seq_pattern = $this->includeN2($sub_seq,0);
                    }
                    elseif ($mismatches == 3) {
                        $sub_seq_pattern = $this->includeN3($sub_seq,0);
                    }
                    else {
                        $sub_seq_pattern = $sub_seq;
                    }

                    $matches = 1;
                    while(preg_match_all("/($sub_seq_pattern)/",substr($sequence,($i+$j*$matches),$j),$out) == 1) {
                        $matches++;
                    }

                    if($matches >= $min_repeats && ($j*$matches) >= $min_length_of_MR) {
                        $results[$counter]["start_position"] = $i;
                        $results[$counter]["length"] = $j;
                        $results[$counter]["repeats"] = $matches;
                        $results[$counter]["sequence"] = substr($sequence,$i,$j*$matches);
                        $counter ++;
                        $i += $j * $matches;
                    }
                }
            }
            return($results);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * When a DNA sequence ("$primer") is provided to this function, as for example "acgt", this function will return
     * a pattern like ".cgt|a.gt|ac.t|acg.". This pattern may be useful to find within a DNA sequence
     * subsequences matching $primer, but allowing one missmach. The parameter $minus
     * is a numeric value which indicates number of bases always maching  the DNA sequence in 3' end.
     * For example, when $minus is 1, the pattern for "acgt" will be  ".cgt|a.gt|ac.t".
     * Check also IncludeN2 and IncludeN3.
     * @param   $primer     DNA sequence (oligonucleotide, primer)
     * @param   $minus      indicates number of bases in 3' which will always much the DNA sequence.
     * @return  string      pattern
     * @throws \Exception
     */
    public function includeN1($primer,$minus)
    {
        try {
            $code = ".".substr($primer,1);
            $wpos = 1;
            while($wpos < strlen($primer) - $minus) {
                $code .= "|".substr($primer,0,$wpos).".".substr($primer,$wpos+1);
                $wpos ++;
            }
            return $code;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Similar to function IncludeN1. When a DNA sequence ("$primer") is provided to this function, as for example "acgt",
     * this function will return a pattern like "..gt|.c.t|.cg.|a..t|a.g.|ac..". This pattern may be useful to find within
     * a DNA sequence subsequences matching $primer, but allowing two missmaches. The parameter $minus
     * is a numeric value which indicates number of bases always maching  the DNA sequence in 3' end.
     * For example, when $minus is 1, the pattern for "acgt" will be  "..gt|.c.t|a..t".
     * Check also IncludeN1 and IncludeN3.
     * @param   $primer     DNA sequence (oligonucleotide, primer)
     * @param   $minus      number of bases in 3' which will always much the DNA sequence.
     * @return  string
     * @throws \Exception
     */
    public function includeN2($primer,$minus)
    {
        try {
            $max = strlen($primer) - $minus;
            $code = "";
            for($i = 0; $i < $max; $i++) {
                for($j=0; $j < $max-$i-1; $j++) {
                    $code .= "|".substr($primer,0,$i).".";
                    $resto = substr($primer,$i+1);
                    $code .= substr($resto,0,$j).".".substr($resto,$j+1);
                }
            }

            $code = substr($code,1);
            return $code;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     *
     * Similar to function IncludeN1 and IncludeN2, but allows two missmaches. The parameter $minus
     * is a numeric value which indicates number of bases always maching  the DNA sequence in 3' end.
     * @param   $primer     DNA sequence (oligonucleotide, primer)
     * @param   $minus      indicates number of bases in 3' which will always much the DNA sequence.
     * @return  string
     * @throws \Exception
     */
    public function includeN3($primer,$minus)
    {
        try {
            $max = strlen($primer) - $minus;
            $code = "";
            for($i = 0; $i < $max; $i++) {
                for($j = 0; $j < $max-$i-1; $j++) {
                    $code.="|".substr($primer,0,$i).".";
                    $resto = substr($primer,$i+1);
                    $code .= substr($resto,0,$j).".".substr($resto,$j+1);
                }
            }
            $code = substr($code,1);
            return $code;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}