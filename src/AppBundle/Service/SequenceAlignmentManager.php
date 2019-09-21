<?php
/**
 * Sequence Alignment Managing
 * Freely inspired by BioPHP's project biophp.org
 * Created 11 february 2019
 * Last modified 21 september 2019
 */
namespace AppBundle\Service;

use AppBundle\Entity\Sequence;
use AppBundle\Service\SequenceManager;

/**
 * SeqAlign - represents the result of an alignment performed by various third-party
 * software such as ClustalW.  The alignment is usually found in a file that uses
 * a particular format. Right now, my code supports only FASTA and CLUSTAL formats.
 *
 * SeqAlign properties and methods allow users to perform post-alignment operations,
 * manipulations, etc.
 * @package AppBundle\Service
 * @author Amélie DUVERNET aka Amelaye <amelieonline@gmail.com>
 */
class SequenceAlignmentManager
{
    /**
     * @var SequenceManager
     */
    private $sequenceManager;

    private $aAlphabet;

    /**
     * @var int
     */
    private $iLength;

    /**
     * @var int
     */
    private $seq_count;

    /**
     * @var int
     */
    private $gap_count;

    /**
     * @var array
     */
    private $seqset;

    /**
     * @var bool
     */
    private $is_flush;

    /**
     * @var string
     */
    private $sFilename;

    /**
     * @var string
     */
    private $sFormat;

    public function __construct(SequenceManager $sequenceManager)
    {
        $this->sequenceManager = $sequenceManager;

        $this->seq_count = 0;
        $this->iLength = 0;
        $this->gap_count = 0;
        $this->is_flush = true;
        $this->seqset = array();
    }

    /**
     * Sets a specific filename : the file to parse
     * @param   string  $sFilename
     */
    public function setFilename($sFilename)
    {
        $this->sFilename = $sFilename;
    }

    /**
     * Sets a specific format : FASTA or CLUSTAL
     * @param   string  $sFormat
     */
    public function setFormat($sFormat)
    {
        $this->sFormat = $sFormat;
    }

    /**
     * Parses Clustal Files and create Sequence object
     */
    public function parseClustal()
    {
        $flines = file($this->sFilename);
        $namelist = array();
        $conserve_line = "";
        $linectr = 0;
        $lastlen = 0;
        $seq = [];
        $iLength = 0;

        $aLines = new \ArrayIterator($flines);

        foreach($aLines as $sLine) {
            $linectr++;
            if ($linectr == 1) {
                continue;
            }
            if (strlen(trim($sLine)) == 0) {
                continue; // ignore blank lines.
            }

            $words = explode(" ", $sLine);

            $wordlines = [];
            foreach($words as $word) {
                if($word != "") {
                    $wordlines[] = str_replace("\n","",$word);
                }
            }

            $seqname = $wordlines[0];
            $seqline = $wordlines[1];

            if (sizeof($wordlines) == 1) {
                $conserve_line .= substr($seqline, 0, $lastlen);
                dump($conserve_line);
                continue;
            }
            if (!in_array($seqname, $namelist)) {
                $namelist[] = $seqname;
                $seq[$seqname] = $seqline;
                $lastlen = strlen(trim($seqline));
            } else {
                $seq[$seqname] .= trim($seqline);
                $lastlen = strlen(trim($seqline));
            }
        }

        $this->seqset = array();
        $gapctr = 0;
        foreach($seq as $key => $value) {
            $seq_obj = new Sequence();
            $seq_obj->setId($key);
            $seq_obj->setSeqlength(strlen($value));
            $seq_obj->setSequence($value);
            $seq_obj->setStart(0);
            $seq_obj->setEnd(strlen($value) - 1);

            $iLength += strlen($value);
            $this->sequenceManager->setSequence($seq_obj);
            $gapctr += $this->sequenceManager->symfreq("-");
            array_push($this->seqset, $seq_obj);
        }
        $this->seq_count = count($namelist);
        $this->iLength = $iLength;
        $this->gap_count = $gapctr;
        $this->is_flush = true;
    }

    /**
     * Parses Fasta Files and create Sequence object
     */
    public function parseFasta()
    {
        $fLines = file($this->sFilename);
        $iSeqCount = $iMaxLength = $iGapCount = $iPrevId = $iPrevLength = 0;
        $bSameLength = true;
        $sSequence = "";
        $aLines = new \ArrayIterator($fLines);

        foreach($aLines as $sLine) {
            if (substr($sLine, 0, 1) == ">") {
                $iSeqCount++;
                $iSeqLength = strlen($sSequence);

                $oSequence = new Sequence();
                $oSequence->setId($iPrevId);
                $oSequence->setSeqlength($iSeqLength);
                $oSequence->setSequence($sSequence);

                $this->sequenceManager->setSequence($oSequence);
                $iGapCount += $this->sequenceManager->symfreq("-");

                if ($iSeqCount > 1) {
                    if ($iSeqLength > $iMaxLength) {
                        $iMaxLength = $iSeqLength;
                    }
                    if (($iSeqCount >= 3) && ($iSeqLength != $iPrevLength)) {
                        $bSameLength = false;
                    }
                    array_push($this->seqset, $oSequence);
                }

                $aWords = preg_split("/[\|\/]/", substr($sLine, 1));
                if(isset($aWords[1])) {
                    $iPrevId = $aWords[1];
                }

                $iPrevLength = $iSeqLength;
                continue;
            } else {
                $sSequence = $sSequence . trim($sLine);
            }
        }

        $iSeqLength = strlen($sSequence);

        $oSequence = new Sequence();
        $oSequence->setId($iPrevId);
        $oSequence->setSeqlength($iSeqLength);
        $oSequence->setSequence($sSequence);

        $this->sequenceManager->setSequence($oSequence);
        $iGapCount += $this->sequenceManager->symfreq("-");

        if ($iSeqCount >= 1) {
            if ($iSeqLength > $iMaxLength) {
                $iMaxLength = $iSeqLength;
            }
            if (($iSeqCount >= 3) && ($iSeqLength != $iPrevLength)) {
                $bSameLength = false;
            }
            array_push($this->seqset, $oSequence);
        }

        $this->seq_count = $iSeqCount;
        $this->iLength = $iMaxLength;
        $this->gap_count = $iGapCount;
        $this->is_flush = $bSameLength;
    }

    /**
     * Main method - Parses FASTA or CLUSTAL file
     */
    public function parseFile()
    {
        if ($this->sFormat == "FASTA") {
            $this->parseFasta();
        } elseif ($this->sFormat == "CLUSTAL") {
            $this->parseClustal();
        }
    }

    /**
     * Rearranges the sequences in an alignment set alphabetically by their sequence id.
     * In addition, you can specify if you wish it be in ascending or descending order via $option.
     * @param type $option
     * @throws \Exception
     */
    public function sort_alpha($option = "ASC")
    {
        try {
            $temp = [];
            foreach($this->seqAlign->getSeqset() as $seqitem)
            {
                $key = $seqitem->id . str_pad($seqitem->start, 9, "0", STR_PAD_LEFT);
                $temp[$key] = $seqitem;
            }

            $option = strtoupper($option);
            if ($option == "ASC") {
                asort($temp);
            } elseif ($option == "DESC") {
                arsort($temp);
            } else {
                throw new \Exception("Invalid argument #1 passed to SORT_ALPHA() method!");
            }

            $temp2 = [];
            foreach($temp as $key => $value) {
                $temp2[] = $value;
            }
            $this->seqAlign->setSeqset($temp2);
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }

    /**
     * Retrieves a particular sequence (identified by its index number) from an alignment set.
     * @param type $index
     * @return type
     * @throws \Exception
     * @group Legacy
     */
    public function fetch($index = "")
    {
        try {
            if (strlen($index) == 0) {
                $index = $this->seqAlign->getSeqptr();
            }
            $oSeqset = $this->seqAlign->getSeqset();
            return $oSeqset[$index];
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Returns the lenght of the longest sequence in an alignment set.
     * @return type
     * @throws \Exception
     * @group Legacy
     */
    public function get_length()
    {
        try {
            $maxlen = 0;
            foreach($this->seqAlign->getSeqset() as $seqitem) {
                if ($seqitem->length > $maxlen) {
                    $maxlen = $seqitem->length;
                }
            }
            return $maxlen;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Counts the number of gaps ("-") found in all sequences in an alignment set.
     * @return type
     * @throws \Exception
     * @group Legacy
     */
    public function get_gap_count()
    {
        try {
            $gapctr = 0;
            foreach($this->seqAlign->getSeqset() as $seqitem) {
                $gapctr += $seqitem->symfreq("-");
            }
            return $gapctr;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Tests if all the sequences in an alignment set have the same length.
     * @return boolean
     * @throws \Exception
     * @group Legacy
     */
    public function get_is_flush()
    {
        try {
            $samelength = TRUE;
            $ctr = 0;
            foreach($this->seqAlign->getSeqset() as $element) {
                $ctr++;
                $currlen = $element->seqlen();
                if ($ctr == 1) {
                    $prevlen = $currlen;
                    continue;
                }
                if ($currlen != $prevlen) {
                    $samelength = FALSE;
                    break;
                }
                $prevlen = $currlen;
            }
            return $samelength;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Returns the character found at a given residue number in a given sequence.
     * @param type $seqidx
     * @param type $res
     * @return boolean
     * @throws \Exception
     * @group Legacy
     */
    public function char_at_res($seqidx, $res)
    {
        try {
            $oSeqset = $this->seqAlign->getSeqset();
            $seqobj = $oSeqset[$seqidx];
            if ($res > $seqobj->end) {
                return FALSE;
            }
            if ($res < $seqobj->start) {
                return FALSE;
            }

            $len = $seqobj->seqlen();
            $nongap_count = $res - $seqobj->start + 1;
            $nongap_ctr = 0;
            for($col = 0; $col < $len; $col++) {
                $currlet = substr($seqobj->sequence, $col, 1);
                if ($currlet == "-") {
                    continue;
                } else {
                    $nongap_ctr++;
                    if ($nongap_ctr == $nongap_count) {
                        return $currlet;
                    }
                }
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Gets the substring between two residues in a sequence that is part of an alignment set.
     * @param type $seqidx
     * @param type $res_beg
     * @param type $res_end
     * @return boolean
     * @throws \Exception
     * @group Legacy
     */
    public function substr_bw_res($seqidx, $res_beg, $res_end = "")
    {
        try {
            $oSeqSet = $this->seqAlign->getSeqset();
            $seqobj = $oSeqSet[$seqidx];
            // Later, you can return a code which identifies the type of error.
            if ($res_end > $seqobj->end) {
                return FALSE;
            }
            if ($res_beg < $seqobj->start) {
                return FALSE;
            }
            if ((gettype($res_end) == "string") && (strlen($res_end) == 0)) {
                $res_end = $seqobj->end;
            }

            $res_begctr = $res_beg - $seqobj->start + 1;
            $res_endctr = $res_end - $seqobj->start + 1;

            $len = $seqobj->seqlen();
            $nongap_ctr = 0;
            $subseq = "";
            for($col = 0; $col < $len; $col++) {
                $currlet = substr($seqobj->sequence, $col, 1);
                if ($currlet != "-") {
                    $nongap_ctr++;
                }
                if (($nongap_ctr >= $res_begctr) && ($nongap_ctr <= $res_endctr)) {
                    $subseq .= $currlet;
                } elseif ($nongap_ctr > $res_endctr) {
                    break;
                }
            }
            return $subseq;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Converts a column number to a residue number in a sequence that is part of an alignment set.
     * @param type $seqidx
     * @param type $col
     * @return boolean|string
     * @throws \Exception
     * @group Legacy
     */
    public function col2res($seqidx, $col)
    {
        try {
            $oSeqset = $this->seqAlign->getSeqset();
            $seqobj = $oSeqset[$seqidx];
            // Later, you can return a code which identifies the type of error.
            if ($col > $seqobj->seqlen() - 1) {
                return FALSE;
            }
            if ($col < 0) {
                return FALSE;
            }

            $nongap_ctr = 0;
            for($i = 0; $i <= $col; $i++) {
                $currlet = substr($seqobj->sequence, $i, 1);
                if ($currlet != "-") {
                    $nongap_ctr++;
                }
            }
            if ($currlet == "-") {
                return "-";
            } else {
                return ($seqobj->start + $nongap_ctr - 1);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Converts a residue number to a column number in a sequence in an alignment set.
     * @param type $seqidx
     * @param type $res
     * @return boolean|int
     * @throws \Exception
     * @group Legacy
     */
    public function res2col($seqidx, $res)
    {
        try {
            $oSeqset = $this->seqAlign->getSeqset();
            $seqobj = $oSeqset[$seqidx];
            // Later, you can return a code which identifies the type of error.
            if ($res > $seqobj->end) {
                return FALSE;
            }
            if ($res < $seqobj->start) {
                return FALSE;
            }

            $len = $seqobj->seqlen();
            $nongap_count = $res - $seqobj->start + 1;
            $nongap_ctr = 0;
            for($col = 0; $col < $len; $col++) {
                $currlet = substr($seqobj->sequence, $col, 1);
                if ($currlet == "-") {
                    continue;
                } else {
                    $nongap_ctr++;
                    if ($nongap_ctr == $nongap_count) {
                        return $col;
                    }
                }
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Returns a subset of consecutive sequences in an alignment set.
     * @param type $beg
     * @param type $end
     * @return \AppBundle\Services\SeqAlign
     * @throws \Exception
     */
    public function subalign($beg, $end)
    {
        try {
            if (($beg < 0) or ($end < 0)) {
                throw new \Exception("Invalid argument passed to SUBALIGN() method!");
            }
            if (($beg > $this->seqAlign->getSeqCount() - 1) or ($end > $this->seqAlign->getSeqCount() - 1)) {
                throw new \Exception("Invalid argument passed to SUBALIGN() method!");
            }

            $new_align = new SequenceAlignment();
            $new_align->setSeqset(array_slice($this->seqAlign->getSeqset(), $beg, $end-$beg+1));
            $new_align->setLength($new_align->get_length());
            $new_align->setSeqCount($end - $beg + 1);
            $new_align->setGapCount($new_align->get_gap_count());
            $new_align->setSeqptr(0);
            $new_align->setIsFlush($new_align->get_is_flush());

            return $new_align;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Returns a set of (possibly non-consecutive) sequences in an alignment set.
     * @return SequenceAlignment
     * @throws \Exception
     */
    public function select()
    {
        try {
            $arglist = func_get_args();
            if (count($arglist) == 0) {
                throw new \Exception("Must pass at least one argument to SELECT() method!");
            }

            $new_seqset = array();
            $new_align = new SequenceAlignment();
            $ctr = 0;
            foreach($arglist as $seqindex) {
                $oSeqset = $this->seqAlign->getSeqset();
                $new_seqset[] = $oSeqset[$seqindex];
                $ctr++;
            }

            $new_align->setSeqset($new_seqset);
            $new_align->setLength($new_align->get_length());
            $new_align->setSeqCount(count($arglist));
            $new_align->setGapCount($new_align->get_gap_count());
            $new_align->setIsFlush($new_align->get_is_flush());
            $new_align->setSeqptr(0);
            return $new_align;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Identifies the positions of variant and invariant (conserved) residues in an alignment set.
     * @param type $threshold
     * @return array
     * @throws \Exception
     */
    public function res_var($threshold = 100)
    {
        try {
            $all_pos    = [];
            $invar_pos  = [];
            $var_pos    = [];
            $oSeqset    = $this->seqAlign->getSeqset();
            $firstseq   = $oSeqset[0];
            $seqlength  = strlen($firstseq->sequence);

            $globfreq = array();
            for($i = 0; $i < count($this->aAlphabet); $i++) {
                $currlet = $this->aAlphabet[$i];
                $globfreq[$currlet] = 0;
            }

            for($i = 0; $i < $seqlength; $i++) {
                $freq = $globfreq;
                for($j = 0; $j < $this->seqAlign->getSeqCount(); $j++) {
                    $currseq = $oSeqset[$j];
                    $currlet = substr($currseq->sequence, $i, 1);
                    $freq[$currlet]++;
                }
                arsort($freq);
                list($key, $value) = each($freq);
                $maxpercent = ($value/$this->seqAlign->getSeqCount()) * 100;
                if ($maxpercent >= $threshold) {
                    array_push($invar_pos, $i);
                } else {
                    array_push($var_pos, $i);
                }
            }
            $all_pos["INVARIANT"] = $invar_pos;
            $all_pos["VARIANT"] = $var_pos;
            return $all_pos;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Returns the consensus string for an alignment set.  See technical reference for details.
     * @param type $threshold
     * @return string
     * @throws \Exception
     */
    public function consensus($threshold = 100)
    {
        try {
            $oSeqset    = $this->seqAlign->getSeqset();
            $resultstr  = "";
            $firstseq   = $oSeqset[0];
            $seqlength  = strlen($firstseq->sequence);

            $globfreq = [];
            for($i = 0; $i < count($this->aAlphabet); $i++) {
                $currlet = $this->aAlphabet[$i];
                $globfreq[$currlet] = 0;
            }

            for($i = 0; $i < $seqlength; $i++) {
                $freq = $globfreq;
                for($j = 0; $j < $this->seqAlign->getSeqCount(); $j++) {
                    $currseq = $oSeqset[$j];
                    $currlet = substr($currseq->sequence, $i, 1);
                    $freq[$currlet]++;
                }
                arsort($freq);
                list($key, $value) = each($freq);
                $maxpercent = ($value/$this->seqAlign->getSeqCount()) * 100;
                if ($maxpercent >= $threshold) {
                    $resultstr = $resultstr . $key;
                } else {
                    $resultstr = $resultstr . "?";
                }
            }
            return $resultstr;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Adds a sequence to an alignment set.
     * @param type $seqobj
     * @return type
     * @throws \Exception
     */
    public function add_seq($seqobj)
    {
        try {
            if (gettype($seqobj) == "object") {
                array_push($this->seqAlign->getSeqset(), $seqobj);
                if ($seqobj->seqlen() > $this->seqAlign->getLength()) {
                    $this->seqAlign->setLength($seqobj->seqlen());
                }

                $this->seqAlign->setGapCount($this->seqAlign->getGapCount() + $seqobj->symfreq("-"));
                if ($seqobj->seqlen() > $this->seqAlign->getLength()) {
                    $this->seqAlign->setLength($seqobj->seqlen());
                }

                if ($this->seqAlign->getIsFlush()) {
                    if ($this->seqAlign->getSeqCount() >= 1) {
                        $firstseq = $this->seqAlign->getSeqset()[0];
                        if ($seqobj->seqlen() != $firstseq->seqlen()) {
                            $this->seqAlign->setIsFlush(false);
                        }
                    }
                }

                $this->seqAlign->setSeqCount($this->seqAlign->getSeqCount() + 1);
                return count($this->seqAlign->getSeqset());
            } elseif (gettype($seqobj) == "string") {
                print "NOT YET OPERATIONAL";
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }


    /**
     * Deletes or removes a sequence from an alignment set.
     * @param type $seqobj
     * @return type
     * @throws \Exception
     */
    public function del_seq($seqobj)
    {
        try {
            $seqid = $seqobj;
            $tempset = array();
            foreach($this->seqAlign->getSeqset() as $element) {
                if ($element->id != $seqid) {
                    array_push($tempset, $element);
                } else {
                    $removed_seq = $element;
                }
            }
            $this->seqAlign->setSeqset($tempset); // Updates the value of the SEQSET property of the SEQALIGN object.
            $this->seqAlign->setSeqCount($this->seqAlign->getSeqCount() - 1); // Updates the value of the SEQ_COUNT property of the SEQALIGN object.

            if ($removed_seq->seqlen() == $this->length) { // Updates the value of the LENGTH property of the SEQALIGN object.
                $maxlen = 0;
                foreach($this->seqAlign->getSeqset() as $element) {
                    if ($element->seqlen() > $maxlen) {
                        $maxlen = $element->seqlen();
                    }
                }
                $this->seqAlign->setLength($maxlen);
            }
            // Updates the value of the GAP_COUNT property of the SEQALIGN object.
            $this->seqAlign->setGapCount($this->seqAlign->setGapCount() - $removed_seq->symfreq("-"));
            // Updates the value of the IS_FLUSH property of the SEQALIGN object.
            if (!$this->seqAlign->getIsFlush()) {
                // Take note that seq_count has already been decremented in the code above.
                if ($this->seqAlign->getSeqCount() <= 1) {
                    $this->seqAlign->setIsFlush(true);
                } else {
                    $samelength = TRUE;
                    $ctr = 0;
                    foreach($this->seqAlign->getSeqset() as $element) {
                        $ctr++;
                        $currlen = $element->seqlen();
                        if ($ctr == 1) {
                            $prevlen = $currlen;
                            continue;
                        }
                        if ($currlen != $prevlen) {
                            $samelength = FALSE;
                            break;
                        }
                        $prevlen = $currlen;
                    }
                    if ($samelength) {
                        $this->seqAlign->setIsFlush(true);
                    }
                }
            }
            // Return the new number of sequences in the alignment set AFTER delete operation.
            return count($this->seqAlign->getSeqset());
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }
}
