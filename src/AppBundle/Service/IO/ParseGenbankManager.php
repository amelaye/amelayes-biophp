<?php
/**
 * Genbank database parsing
 * Freely inspired by BioPHP's project biophp.org
 * Created 24 november 2019
 * Last modified 24 november 2019
 */
namespace AppBundle\Service\IO;

use AppBundle\Entity\Sequencing\Authors;
use AppBundle\Entity\Sequencing\GbFeatures;
use AppBundle\Entity\Sequencing\Keywords;
use AppBundle\Entity\Sequencing\Reference;
use AppBundle\Entity\Sequencing\Accession;

/**
 * Class ParseGenbankManager
 * @package AppBundle\Service
 * @author Amélie DUVERNET aka Amelaye <amelieonline@gmail.com>
 */
final class ParseGenbankManager extends ParseDbAbstractManager
{
    /**
     * @var array
     */
    private $aLines;

    /**
     * Parses a GenBank data file and returns a Seq object containing parsed data.
     * @param   array       $aFlines        The lines the script has to parse
     * @throws \Exception
     */
    public function parseDataFile($aFlines)
    {
        try {
            $this->aLines = new \ArrayIterator($aFlines); // <3

            foreach($this->aLines as $lineno => $linestr) {

                switch(trim(substr($this->aLines->current(),0,12))) {
                    case "LOCUS":
                        $this->parseLocus();
                        break;
                    case "DEFINITION":
                        $this->parseDefinition($aFlines);
                        break;
                    case "ORGANISM":
                        $this->sequence->setOrganism(trim(substr($linestr,12)));
                        break;
                    case "VERSION":
                        $this->parseVersion();
                        break;
                    case "KEYWORDS":
                        $this->parseKeywords();
                        break;
                    case "ACCESSION":
                        $this->parseAccession();
                        break;
                    case "FEATURES":
                        while(1) {
                            // Verify next line
                            $sHead = trim(substr($aFlines[$this->aLines->key()+1],0, 20));
                            $aFields = ["source", "gene", "exon", "CDS", "misc_feature"];
                            if($sHead != "" && !in_array($sHead, $aFields)) {
                                break;
                            }
                            $this->aLines->next();
                            $sHead = trim(substr($this->aLines->current(), 0, 20));
                            if(in_array($sHead, $aFields)) {
                                $this->parseFeatures($aFlines, $sHead);
                            }
                        }
                        break;
                    case "REFERENCE":
                        $this->parseReferences($aFlines);
                        break;
                    case "SOURCE":
                        $this->sequence->setSource(trim(substr($linestr, 12)));
                        break;
                    case "ORIGIN":
                        $sWords = "";
                        while(1) {
                            $this->aLines->next();
                            $sWords .= trim(substr($this->aLines->current(),9))." ";
                            $sHead = trim(substr($aFlines[$this->aLines->key()+1],0, 20));
                            if($sHead == '//') {
                                break;
                            }
                        }
                        $this->sequence->setSequence(trim($sWords));
                        break;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * @param   array       $aFlines    The lines the script has to parse
     * @throws  \Exception
     */
    private function parseReferences($aFlines)
    {
        try {
            $oReference = new Reference();
            $aWords = preg_split("/\s+/", trim(substr($this->aLines->current(),12)));
            $oReference->setRefno($aWords[0]);
            array_shift($aWords);
            $oReference->setBaseRange(implode(" ", $aWords));

            $sAuthors = $sTitle = $sJournal = $sMedline = $sPubmed = $sRemark = "";
            $this->aLines->next();

            if(trim(substr($this->aLines->current(),0,12)) == "AUTHORS") {
                $this->seekReferences($sAuthors);
                $sAuthors = trim($sAuthors);
                $sAuthors = str_replace(" and ", ",", $sAuthors);
                $sAuthors = str_replace(".", "", $sAuthors);
                $aAuthors = explode(",",$sAuthors);
                foreach($aAuthors as $sAuthor) {
                    $oAuthor = new Authors();
                    $oAuthor->setPrimAcc($this->sequence->getPrimAcc());
                    $oAuthor->setRefno($oReference->getRefno());
                    $oAuthor->setAuthor(trim($sAuthor));
                    $this->authors[] = $oAuthor;
                }
            }

            if(trim(substr($this->aLines->current(),0,12)) == "CONSRTM") {
                $this->aLines->next();
            }

            if(trim(substr($this->aLines->current(),0,12)) == "TITLE") {
                $this->seekReferences($sTitle);
                $oReference->setTitle(trim($sTitle));
            }

            if(trim(substr($this->aLines->current(),0,12)) == "JOURNAL") {
                $this->seekReferences($sJournal);
                $oReference->setJournal(trim($sJournal));
            }

            if(trim(substr($this->aLines->current(),0,12)) == "MEDLINE") {
                $this->seekReferences($sMedline);
                $oReference->setMedline($sMedline);
            }

            if(trim(substr($this->aLines->current(),0,12)) == "PUBMED") {
                $aPubmed = preg_split("/\s+/", trim(substr($this->aLines->current(), 12)));
                $sPubmed = trim($sPubmed." ".implode(" ", $aPubmed));
                $oReference->setPubmed($sPubmed);
                // If reference following, don't jump line
                if(trim(substr($aFlines[$this->aLines->key()+1],0, 12)) != "REFERENCE") {
                    $this->aLines->next();
                }
            }

            if(trim(substr($this->aLines->current(),0,12)) == "REMARK") {
                while(1) {
                    $sRemark .= " ".trim(substr($this->aLines->current(), 12));
                    // If reference following, don't jump line
                    if(trim(substr($aFlines[$this->aLines->key()+1],0, 12)) != "REFERENCE") {
                        $this->aLines->next();
                        $sHead = trim(substr($this->aLines->current(), 0, 12));
                        if ($sHead != "") {
                            break;
                        }
                    } else {
                        break;
                    }
                }
                $oReference->setRemark(trim($sRemark));
            }
           $this->references[] = $oReference;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Parse every multi-line fields from REFERENCES
     * @param   string          $sReferenceProperty     The references part
     * @return  string
     * @throws  \Exception
     */
    private function seekReferences(&$sReferenceProperty)
    {
        try {
            while(1) {
                $sReferenceProperty .= " ".trim(substr($this->aLines->current(), 12));
                $this->aLines->next();
                $head = trim(substr($this->aLines->current(), 0, 12));
                if ($head != "") {
                    break;
                }
            }
            return $sReferenceProperty;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses line LOCUS
     * @return      mixed
     * @throws      \Exception
     */
    private function parseLocus()
    {
        try {
            $this->sequence->setPrimAcc(trim(substr($this->aLines->current(), 12, 16)));
            $this->gbSequence->setPrimAcc($this->sequence->getPrimAcc());

            $this->sequence->setSeqlength(trim(substr($this->aLines->current(), 29, 11)) * 1);
            $this->sequence->setMoltype(trim(substr($this->aLines->current(), 47, 6)));

            switch(substr($this->aLines->current(), 44, 3)) {
                case "ss-":
                    $this->gbSequence->setStrands("SINGLE");
                    break;
                case "ds-":
                    $this->gbSequence->setStrands("DOUBLE");
                    break;
                case "ms-":
                    $this->gbSequence->setStrands("MIXED");
                    break;
            }

            $this->gbSequence->setTopology(strtoupper(trim(substr($this->aLines->current(), 55, 8))));
            $this->gbSequence->setDivision(strtoupper(trim(substr($this->aLines->current(), 64, 3))));
            $this->sequence->setDate(strtoupper(trim(substr($this->aLines->current(), 68, 11))));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses DEFINITION field
     * @ param      array            $flines
     * @throws      \Exception
     */
    private function parseDefinition($flines)
    {
        try {
            $wordarray = explode(" ", $this->aLines->current());
            array_shift($wordarray);
            $sDefinition = trim(implode(" ", $wordarray));
            while(1) {
                $head = trim(substr($flines[$this->aLines->key()+1],0, 12));
                if($head != "") {
                    break;
                }
                $this->aLines->next();
                $sDefinition .= " ".trim($this->aLines->current());
            }
            $this->sequence->setDescription($sDefinition);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses VERSION field
     * @throws      \Exception
     */
    private function parseVersion()
    {
        try {
            $wordarray = preg_split("/\s+/", trim($this->aLines->current()));
            $this->gbSequence->setVersion($wordarray[1]);
            if (count($wordarray) == 3) {
                $this->gbSequence->setNcbiGiId($wordarray[2]);
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses KEYWORDS field
     * @throws      \Exception
     */
    private function parseKeywords()
    {
        try {
            $wordarray = preg_split("/\s+/", trim($this->aLines->current()));
            array_shift($wordarray);
            $wordarray = preg_split("/;+/", implode(" ", $wordarray));
            if ($wordarray[0] != ".") {
                foreach($wordarray as $word) {
                    $oKeyword = new Keywords();
                    $oKeyword->setPrimAcc($this->sequence->getPrimAcc());
                    $oKeyword->setKeywords($word);
                    $this->keywords[] = $oKeyword;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses ACCESSION field
     * @throws      \Exception
     */
    private function parseAccession()
    {
        try {
            $wordarray = preg_split("/\s+/", trim($this->aLines->current()));
            $this->sequence->setPrimAcc($wordarray[1]);
            array_shift($wordarray);
            array_shift($wordarray);
            foreach($wordarray as $word) {
                $oAccession = new Accession();
                $oAccession->setPrimAcc($wordarray[1]);
                $oAccession->setAccession($word);
                $this->accession[] = $oAccession;
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Parses each fields for FEATURES
     * @param   array   $aFlines
     * @param   string  $sField
     * @throws  \Exception
     */
    private function parseFeatures($aFlines, $sField)
    {
        try {
            $sKey = $sField ." ". trim(substr($this->aLines->current(), 20));
            $this->aLines->next();
            $sLine = trim(substr($this->aLines->current(), 20));
            while (1) {
                // If line begins with  /
                // Adding line in array
                if(trim($aFlines[$this->aLines->key()+1])[0] == "/") {
                    $sLine = str_replace("/","",trim($sLine));
                    $aLine = explode("=",str_replace('"',"",$sLine));
                    $oGbFeature = new GbFeatures();
                    $oGbFeature->setPrimAcc($this->sequence->getPrimAcc());
                    $oGbFeature->setFtKey($sKey);
                    $oGbFeature->setFtQual($aLine[0]);
                    $oGbFeature->setFtValue($aLine[1]);
                    $this->gbFeatures[] = $oGbFeature;
                    $sLine = ""; // RAZ
                }

                $this->aLines->next();
                $sLine .= " ".trim(substr($this->aLines->current(), 20));

                $sHead = trim(substr($aFlines[$this->aLines->key()+1],0, 12));
                if($sHead != "") { // Stop if we change feature
                    $sLine = str_replace("/","",trim($sLine));
                    $aLine = explode("=",str_replace('"',"",$sLine));
                    $oGbFeature = new GbFeatures();
                    $oGbFeature->setPrimAcc($this->sequence->getPrimAcc());
                    $oGbFeature->setFtKey($sKey);
                    $oGbFeature->setFtQual($aLine[0]);
                    $oGbFeature->setFtValue($aLine[1]);
                    $this->gbFeatures[] = $oGbFeature;
                    break;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
