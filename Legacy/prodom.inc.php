<?php
// prodom.inc.php

class ProtFam_Prodom
{
    var $entry_no;
    var $accession;
    var $release;
    var $domain_count;
    var $freq_names;       // an associative array e.g. array("FDAM" => 2, ...)
    var $keywords;         // a simple 1D array e.g. array("DNA-BINDING", "PROTEASE", ...)
}

function parse_protfam_prodom($flines)
{
    // initialize variables here

    while ( list($no, $linestr) = each($flines) )
    { // OPENS 1st (outermost) while ( list($no, $linestr) = each($flines) )
        $linelabel = left($linestr, 2);
        $linedata = trim(substr($linestr, 5));
        $lineend = right($linedata, 1);

        /* ID - IDENTIFIER data field - contains the ENTRY_NO, RELEASE, and DOMAIN_COUNT data items.
        We assume here that all three data items are mandatory.
           Example: ID 20167 p2002.1                           10 seq.
        */

        if ($linelabel == "ID")
        {
            // we redefine $linedata for the ID line because it starts at position index 3 instead of 5.
            $linedata = trim(substr($linestr, 3));
            $id_tokens = preg_split("/\s+/", $linedata, -1, PREG_SPLIT_NO_EMPTY);
            // "20167", "p2002.1", "10", "seq"
            $entry_no = trim($id_tokens[0]);
            // we remove the prefix "p" from the second token to get the RELEASE data item.
            $release = substr(trim($id_tokens[1]), 1);
            // we basically ignore the fourth token, which we assume is always "seq".
            $domain_count = (int) (trim($id_tokens[2]));
        }

        /* AC - ACCESSION data field - exactly one entry (word) in one line
           Example: AC   PD266930
        */

        if ($linelabel == "AC") $accession = $linedata;

        /* KW - KEYWORD data field
           Syntax: KW [FREQUENT_NAME(OCCURRENCE)...] // KEYWORD [KEYWORD ...]
           Example: KW   FADR(2) Y586(1) // COMPLETE PROTEOME DNA-BINDING FATTY TRANSCRIPTION REGULATION METABOLISM REGULATOR ACID ACTIVATOR
        */

        if ($linelabel == "KW")
        {
            $kw_tokens = preg_split("/\/\//", $linedata, -1, PREG_SPLIT_NO_EMPTY);
            // E.g. $kw_tokens is "FADR(2) Y586(1)", "COMPLETE PROTEOME DNA-BINDING..."
            $freqnames = trim($kw_tokens[0]);
            $freqname_tokens = preg_split("/\s+/", $freqnames, -1, PREG_SPLIT_NO_EMPTY);
            // E.g. $freqname_tokens is array( "FADR(2)", "Y586(1))" )
            // Because we use \s+ as the separator, we are sure that each element in $freqname_tokens array
            // has no trailing/leading whitespaces, so no need to array_walk(..., "trim_element") it.
            $aFreqNames = array();
            foreach($freqname_tokens as $seqname)
            {
                $seqname_tokens = preg_split("/\(/", $seqname, -1, PREG_SPLIT_NO_EMPTY);
                // e.g. "FADR", "2)"
                $seqname = $seqname_tokens[0];
                $seqfreq = (int) (substr($seqname_tokens[1], 0, strlen($seqname_tokens[1])-1));
                // we store $seqname and $seqfreq in an associative array called $aFreqNames;
                $aFreqNames[$seqname] = $seqfreq;
            }
            $aKeywords = preg_split("/\s+/", trim($kw_tokens[1]), -1, PREG_SPLIT_NO_EMPTY);
        }

        if ($linelabel == "//") break;
    }

    $oProtFam = new ProtFam_Prodom();
    $oProtFam->entry_no = $entry_no;
    $oProtFam->accession = $accession;
    $oProtFam->release = $release;
    $oProtFam->domain_count = $domain_count;
    $oProtFam->freq_names = $aFreqNames;
    $oProtFam->keywords = $aKeywords;

    return $oProtFam;
}

/*
ID 20167 p2002.1                           10 seq.
AC   PD266930
KW   FADR(2) Y586(1) // COMPLETE PROTEOME DNA-BINDING FATTY TRANSCRIPTION REGULATION METABOLISM REGULATOR ACID ACTIVATOR
LA   74
ND   10
CC   -!- DIAMETER:      119 PAM
CC   -!- RADIUS OF GYRATION:    53 PAM
CC   -!- SEQUENCE CLOSEST TO CONSENSUS: Q8ZEL9_YERPE 5-78 (distance:15 PAM)
DC   This family was generated by psi-blast, with a profile built from the seed aligment of the following SCOP FAMILY
DC   a.4.5.6
AL P09371|FADR_ECOLI            4    77 0.22 AQSPAGFAEEYIIESIWNNRFPPGTILPAERELSELIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNNFWETS
AL Q8ZP15|Q8ZP15_SALTY          5    78 0.22 AQSPAGFAEEYIIESIWNNRFPPGTILPAERELSELIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNNFWETS
AL Q8ZEL9|Q8ZEL9_YERPE          5    78 0.22 AQSPAGFAEEYIIESIWNNRFPPGSILPAERELSELIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNNFWETS
AL Q8Z685|Q8Z685_SALTI          5    78 0.35 AQSPAGFAEEYIIESIWNNCFPPGTILPAERELSELIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNNFWETS
AL Q9KQU8|Q9KQU8_VIBCH          5    78 0.62 AKSPAGFAEKYIIESIWNGRFPPGSILPAERELSELIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNQFMETS
AL Q9CPJ0|Q9CPJ0_PASMU         10    83 0.77 AQSPAGLAEEYIVRSIWNNHFPPGSDLPAERELAEKIGVTRTTLREVLQRLARDGWLNIQHGKPTKVNNIWETS
AL P44705|FADR_HAEIN           10    81 1.08 AQSPAALAEEYIVKSIWQDVFPAGSNLPSERDLADKIGVTRTTLREVLQRLARDGWLTIQHGKPTKVNNIWD..
AL O07792|Y586_MYCTU           17    77 2.08 .........EQIATDVLTGEMPPGEALPSERRLAELLGVSRPAVREALKRLSAAGLVEVRQGDVTTVRDF....
AL Q11159|Y494_MYCTU           27    77 2.21 ...........IADAILDGVFPPGSTLPPERDLAERLGVNRTSLRQGLARLQQMGLIEVRHG............
AL Q8XFI2|Q8XFI2_SALTY         59   109 2.23 ...........IIKLINDNIFPPGTFLPPERELAKQLGVSRASLREALIVLEISGWIVIQSG............
CO                                           AQSPAGFAEEYIVKSIWDGVFPPGSTLPPERELAERLGVSRTSLREALQRLERDGWIEIQHGKPTKVNNFWETS
DR   INTERPRO;    IPR000524 "Bacterial regulatory proteins, GntR"
DR   PfamA;       PF00392 gntR
DR   PROSITE;     PS00043 PDOC00042 HTH_GNTR_FAMILY (27-51)
DR   PDB;         1H9T chain B  (5-78) Q8ZP15_SALTY (5-78),1HW1 chain A (5-78),1HW1 chain B (5-78)
DR   PDB;         1H9T chain A  (5-78) Q8ZP15_SALTY (5-78)
//

*/
?>