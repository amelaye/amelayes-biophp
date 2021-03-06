<?php
require_once("seq.inc");

/* SeqAlign - represents the result of an alignment performed by various third-party 
      software such as ClustalW.  The alignment is usually found in a file that uses
		a particular format. Right now, my code supports only FASTA and CLUSTAL formats.
   
   SeqAlign properties and methods allow users to perform post-alignment operations, 
	manipulations, etc.
*/

class SeqAlign
{ // OPENS class SeqAlign
var $length;
var $seq_count;
var $gap_count;
var $seqset;
var $seqptr;
var $is_flush;

// sort_alpha rearranges the sequences in an alignment set alphabetically by their sequence id.
// In addition, you can specify if you wish it be in ascending or descending order via $option.
function sort_alpha($option = "ASC")
	{
	$temp = array();
	foreach($this->seqset as $seqitem)
		{
		$key = $seqitem->id . str_pad($seqitem->start, 9, "0", STR_PAD_LEFT);
		$temp[$key] = $seqitem;
		}

	$option = strtoupper($option);
	if ($option == "ASC") asort($temp);
	elseif ($option == "DESC") arsort($temp);
	else die("Invalid argument #1 passed to SORT_ALPHA() method!");

	$temp2 = array();
	foreach($temp as $key => $value)
		$temp2[] = $value;
	$this->seqset = $temp2;
	}

// first() moves the sequence pointer to the first sequence in the alignment set.
function first()
	{
	$this->seqptr = 0;
	}

// last() moves the sequence pointer to the last sequence in the alignment set.
function last()
	{
	$this->seqptr = $this->seq_count - 1;
	}

// prev() moves the sequence pointer to the sequence before the current one.
function prev()
	{
	if ($this->seqptr > 0) $this->seqptr--;
	}

// next() moves the sequence pointer to the sequence after the current one.
function next()
	{
	if ($this->seqptr < $this->seq_count-1) $this->seqptr++;
	}

// fetch() retrieves a particular sequence (identified by its index number) from an alignment set.
function fetch($index = "")
	{
	if (strlen($index) == 0) $index = $this->seqptr;
	return $this->seqset[$index];
	}

// get_length() returns the lenght of the longest sequence in an alignment set.
function get_length()
	{
	$maxlen = 0;
	foreach($this->seqset as $seqitem)
		if ($seqitem->length > $maxlen) $maxlen = $seqitem->length;
	return $maxlen;
	}

// get_gap_count() counts the number of gaps ("-") found in all sequences in an alignment set. 
function get_gap_count()
	{
	$gapctr = 0;
	foreach($this->seqset as $seqitem)
		$gapctr += $seqitem->symfreq("-");
	return $gapctr;
	}

// get_is_flush() tests if all the sequences in an alignment set have the same length.
function get_is_flush()
	{
	$samelength = TRUE;
	$ctr = 0;
	foreach($this->seqset as $element)
		{
		$ctr++;
		$currlen = $element->seqlen();
		if ($ctr == 1)
			{
			$prevlen = $currlen;
			continue;
			}
		if ($currlen != $prevlen)
			{
			$samelength = FALSE;
			break;
			}
		$prevlen = $currlen;
		}
	return $samelength;
	}

// char_at_res() returns the character found at a given residue number in a given sequence.
function char_at_res($seqidx, $res)
	{
	$seqobj = $this->seqset[$seqidx];
	if ($res > $seqobj->end) return FALSE;
	if ($res < $seqobj->start) return FALSE;

	$len = $seqobj->seqlen();
	$nongap_count = $res - $seqobj->start + 1;
	$nongap_ctr = 0;
	for($col = 0; $col < $len; $col++)
		{
		$currlet = substr($seqobj->sequence, $col, 1);
		if ($currlet == "-") continue;
		else
			{
			$nongap_ctr++;
			if ($nongap_ctr == $nongap_count) return $currlet;
			}
		}
	}

// substr_bw_res() gets the substring between two residues in a sequence that is part of an alignment set.
function substr_bw_res($seqidx, $res_beg, $res_end = "")
	{
	$seqobj = $this->seqset[$seqidx];
	// Later, you can return a code which identifies the type of error.
	if ($res_end > $seqobj->end) return FALSE;
	if ($res_beg < $seqobj->start) return FALSE;
	if ( (gettype($res_end) == "string") and (strlen($res_end) == 0) )
		$res_end = $seqobj->end;

	$res_begctr = $res_beg - $seqobj->start + 1;
	$res_endctr = $res_end - $seqobj->start + 1;

	$len = $seqobj->seqlen();
	$nongap_ctr = 0;
	$subseq = "";
	for($col = 0; $col < $len; $col++)
		{
		$currlet = substr($seqobj->sequence, $col, 1);
		if ($currlet != "-") $nongap_ctr++;
		if ( ($nongap_ctr >= $res_begctr) and
			  ($nongap_ctr <= $res_endctr) )
			$subseq .= $currlet;
		elseif ($nongap_ctr > $res_endctr) break;
		}
	return $subseq;
	}

// col2res() converts a column number to a residue number in a sequence that is part of an alignment set.
function col2res($seqidx, $col)
	{
	$seqobj = $this->seqset[$seqidx];
	// Later, you can return a code which identifies the type of error.
	if ($col > $seqobj->seqlen() - 1) return FALSE;
	if ($col < 0) return FALSE;

	$nongap_ctr = 0;
	for($i = 0; $i <= $col; $i++)
		{
		$currlet = substr($seqobj->sequence, $i, 1);
		if ($currlet != "-") $nongap_ctr++;
		}
	if ($currlet == "-") return "-";
	else return ($seqobj->start + $nongap_ctr - 1); 
	}

// res2col() converts a residue number to a column number in a sequence in an alignment set.
function res2col($seqidx, $res)
	{
	$seqobj = $this->seqset[$seqidx];
	// Later, you can return a code which identifies the type of error.
	if ($res > $seqobj->end) return FALSE;
	if ($res < $seqobj->start) return FALSE;

	$len = $seqobj->seqlen();
	$nongap_count = $res - $seqobj->start + 1;
	$nongap_ctr = 0;
	for($col = 0; $col < $len; $col++)
		{
		$currlet = substr($seqobj->sequence, $col, 1);
		if ($currlet == "-") continue;
		else
			{
			$nongap_ctr++;
			if ($nongap_ctr == $nongap_count) return $col;
			}
		}
	}

// subalign() returns a subset of consecutive sequences in an alignment set.
function subalign($beg, $end)
	{
	if (($beg < 0) or ($end < 0)) die("Invalid argument passed to SUBALIGN() method!");
	if (($beg > $this->seq_count-1) or ($end > $this->seq_count-1))
		die("Invalid argument passed to SUBALIGN() method!");

	$new_seqset = array();
	$new_align = new SeqAlign();
	$new_align->seqset = array_slice($this->seqset, $beg, $end-$beg+1);
	$new_align->length = $new_align->get_length();
	$new_align->seq_count = $end - $beg + 1;
	$new_align->gap_count = $new_align->get_gap_count();
	$new_align->seqptr = 0;
	$new_align->is_flush = $new_align->get_is_flush();
	return $new_align;
	}

// select() returns a set of (possibly non-consecutive) sequences in an alignment set.
function select()
	{
	$arglist = func_get_args();
	if (count($arglist) == 0) die("Must pass at least one argument to SELECT() method!");

	$new_seqset = array();
	$new_align = new SeqAlign();
	$ctr = 0;
	foreach($arglist as $seqindex)
		{
		$new_seqset[] = $this->seqset[$seqindex];
		$ctr++;
		}
	$new_align->seqset = $new_seqset;
	$new_align->length = $new_align->get_length();
	$new_align->seq_count = count($arglist);
	$new_align->gap_count = $new_align->get_gap_count();
	$new_align->seqptr = 0;
	$new_align->is_flush = $new_align->get_is_flush();
	return $new_align;
	}

// res_var() identifies the positions of variant and invariant (conserved) residues in an alignment set.
function res_var($threshold = 100)
	{
	// for now, assume all the sequences are equal in length.
	$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
							'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

	$all_pos = array();
	$invar_pos = array();
	$var_pos = array();
	$firstseq = $this->seqset[0];
	$seqlength = strlen($firstseq->sequence);

	$globfreq = array();
	for($i = 0; $i < count($alphabet); $i++)
		{
		$currlet = $alphabet[$i];
		$globfreq[$currlet] = 0;
		}

	for($i = 0; $i < $seqlength; $i++)
		{
		$freq = $globfreq;
		for($j = 0; $j < $this->seq_count; $j++)
			{
			$currseq = $this->seqset[$j];
			$currlet = substr($currseq->sequence, $i, 1);
			$freq[$currlet]++;
			}
		arsort($freq);
		list($key, $value) = each($freq);
		$maxpercent = ($value/$this->seq_count) * 100;
		if ($maxpercent >= $threshold) array_push($invar_pos, $i);
		else array_push($var_pos, $i);
		}
	$all_pos["INVARIANT"] = $invar_pos;
	$all_pos["VARIANT"] = $var_pos;
	return $all_pos;
	}

// consensus() returns the consensus string for an alignment set.  See technical reference for details.
function consensus($threshold = 100)
	{
	// for now, assume all the sequences are equal in length.
	$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
							'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

	$resultstr = "";
	$firstseq = $this->seqset[0];
	$seqlength = strlen($firstseq->sequence);

	$globfreq = array();
	for($i = 0; $i < count($alphabet); $i++)
		{
		$currlet = $alphabet[$i];
		$globfreq[$currlet] = 0;
		}

	for($i = 0; $i < $seqlength; $i++)
		{
		$freq = $globfreq;
		for($j = 0; $j < $this->seq_count; $j++)
			{
			$currseq = $this->seqset[$j];
			$currlet = substr($currseq->sequence, $i, 1);
			$freq[$currlet]++;
			}
		arsort($freq);
		list($key, $value) = each($freq);
		$maxpercent = ($value/$this->seq_count) * 100;
		if ($maxpercent >= $threshold) $resultstr = $resultstr . $key;
		else $resultstr = $resultstr . "?";
		}
	return $resultstr;
	}

// add_seq() adds a sequence to an alignment set.
function add_seq($seqobj)
	{
	if (gettype($seqobj) == "object")
		{
		array_push($this->seqset, $seqobj);
		if ($seqobj->seqlen() > $this->length) $this->length = $seqobj->seqlen();
		$this->gap_count += $seqobj->symfreq("-");
		if ($seqobj->seqlen() > $this->length) $this->length = $seqobj->seqlen();

		if ($this->is_flush == TRUE)
			{
			if ($this->seq_count >= 1)
				{
				$firstseq = $this->seqset[0];
				if ($seqobj->seqlen() != $firstseq->seqlen()) $this->is_flush = FALSE;
				}
			}
		$this->seq_count++;
		return count($this->seqset);
		}
	elseif (gettype($seqobj) == "string")
		{
		print "NOT YET OPERATIONAL";
		}
	}

// del_seq() deletes or removes a sequence from an alignment set.
function del_seq($seqobj)
	{
	$seqid = $seqobj;
	$tempset = array();
	foreach($this->seqset as $element)
		{
		if ($element->id != $seqid) array_push($tempset, $element);
		else $removed_seq = $element;
		}
	// Updates the value of the SEQSET property of the SEQALIGN object.
	$this->seqset = $tempset;
	// Updates the value of the SEQ_COUNT property of the SEQALIGN object.
	$this->seq_count--;
	// Updates the value of the LENGTH property of the SEQALIGN object.
	if ($removed_seq->seqlen() == $this->length)
		{
		$maxlen = 0;
		foreach($this->seqset as $element)
			if ($element->seqlen() > $maxlen) $maxlen = $element->seqlen();
		$this->length = $maxlen;
		}
	// Updates the value of the GAP_COUNT property of the SEQALIGN object.
	$this->gap_count -= $removed_seq->symfreq("-");
	// Updates the value of the IS_FLUSH property of the SEQALIGN object.
	if ($this->is_flush == FALSE)
		{
		// Take note that seq_count has already been decremented in the code above.
		if ($this->seq_count <= 1) $this->is_flush = TRUE;
		else
			{
			$samelength = TRUE;
			$ctr = 0;
			foreach($this->seqset as $element)
				{
				$ctr++;
				$currlen = $element->seqlen();
				if ($ctr == 1)
					{
					$prevlen = $currlen;
					continue;
					}
				if ($currlen != $prevlen)
					{
					$samelength = FALSE;
					break;
					}
				$prevlen = $currlen;
				}
			if ($samelength == TRUE) $this->is_flush = TRUE;
			}
		}
	// Return the new number of sequences in the alignment set AFTER delete operation.
	return count($this->seqset);
	}

// SeqAlign() is the constructor method for the SeqAlign class.  It initializes class properties.
function SeqAlign($filename = "", $format = "FASTA")
	{ // OPENS function SeqAlign        
	if (strlen($filename) == 0)
		{
		$this->seq_count = 0;
		$this->length = 0;
		$this->seqptr = 0;
		$this->gap_count = 0;
		$this->is_flush = TRUE;
		$this->seqset = array();
		return;
		}

	if ($format == "FASTA")
		{
		$flines = file($filename);
		$seqctr = 0;
		$maxlen = 0;
		$maxctr = 0;
		$gapctr = 0;
		$this->seqset = array();
		$samelength = TRUE;
	
		while ( list($no, $linestr) = each($flines) )
			{ // OPENS while ( list($no, $linestr) = each($flines) )
			if (substr($linestr, 0, 1) == ">")
				{ // start of a new sequence
				$seqctr++;
				$seqlen = strlen($seqstr);
	
				$seq_obj = new seq();
				$seq_obj->id = $prev_id;
				$seq_obj->length = $seqlen;
				$seq_obj->sequence = $seqstr;
				$seq_obj->start = $prev_start;
				$seq_obj->end = $prev_end;
				$localgaps = $seq_obj->symfreq("-");
				$gapctr += $seq_obj->symfreq("-");
	
				if ($seqctr > 1)
					{
					if ($seqlen > $maxlen) $maxlen = $seqlen;
					if (($seqctr >= 3) and ($seqlen != $prev_len))
						$samelength = FALSE;
					array_push($this->seqset, $seq_obj);
					}
				$seqstr = "";
	
				$words = preg_split("/[\>\/]/", substr($linestr, 1));
				$prev_id = $words[0];
	
				$indexes = preg_split("/-/", $words[1]);
				$prev_start = $indexes[0];
				$prev_end = $indexes[1];
				$prev_len = $seqlen;
				continue;
				}
			else
				{
				$seqstr = $seqstr . trim($linestr);
				}
			} // CLOSES while ( list($no, $linestr) = each($flines) ) 
	
			$seqlen = strlen($seqstr);
			$seq_obj = new seq();
			$seq_obj->id = $prev_id;
			$seq_obj->start = $prev_start;
			$seq_obj->end = $prev_end;
			$seq_obj->length = $seqlen;
			$seq_obj->sequence = $seqstr;
			$localgaps = $seq_obj->symfreq("-");
			$gapctr += $seq_obj->symfreq("-");
	
			if ($seqctr > 1)
				{
 				if ($seqlen > $maxlen) $maxlen = $seqlen;
				if (($seqctr >= 3) and ($seqlen != $prev_len))
					$samelength = FALSE;
				array_push($this->seqset, $seq_obj);
				}
	
		$this->seq_count = $seqctr;
		$this->length = $maxlen;
		$this->seqptr = 0;
		$this->gap_count = $gapctr;
		$this->is_flush = $samelength;
		} // CLOSES if ($format == "FASTA") 
	elseif ($format == "CLUSTAL")
		{ // OPENS elseif ($format == "CLUSTAL") 
		$flines = file($filename);
		$namelist = array();
		$conserve_line = "";
		$linectr = 0;
		while( list($no, $linestr) = each($flines) )
			{ // OPENS while( list($no, $linestr) = each($flines) )
			$linectr++;
			if ($linectr == 1) continue; // skip the first line.
			if (strlen(trim($linestr)) == 0) continue; // ignore blank lines.
		
			$seqname = trim(substr($linestr, 0, 16));       
			$seqline = substr($linestr, 16, 60);
		
			if (strlen(trim($seqname)) == 0)
				{
				$conserve_line .= substr($seqline, 0, $lastlen);
				continue;
				}
			if (in_array($seqname, $namelist) == FALSE)
				{ 
				$namelist[] = $seqname; 
				$seq[$seqname] = $seqline;                 
				$lastlen = strlen(trim($seqline));         						
				}  
			else
				{ 
				$seq[$seqname] .= trim($seqline);
				$lastlen = strlen(trim($seqline));         
				} 
			} // CLOSES while( list($no, $linestr) = each($flines) )

			$this->seqset = array();			
			$gapctr = 0;
			foreach($seq as $key => $value)
				{
				$seq_obj = new seq();
				$seq_obj->id = $key;
				$seq_obj->length = strlen($value);
				$seq_obj->sequence = $value;
				$seq_obj->start = 0;
				$seq_obj->end = $seq_obj->length - 1;
				$gapctr += $seq_obj->symfreq("-");
				array_push($this->seqset, $seq_obj);						                  
				}											
			$this->seq_count = count($namelist);
			$this->length = strlen($conserve_line);
			$this->seqptr = 0;
			$this->gap_count = $gapctr;
			$this->is_flush = TRUE;
		} // CLOSES elseif ($format == "CLUSTAL") 
	} // CLOSES function SeqAlign
} // CLOSES class SeqAlign
?>