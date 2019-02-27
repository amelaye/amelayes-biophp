<?php
/**
 * Protein To DNA Functions
 * @author Amélie DUVERNET akka Amelaye
 * Inspired by BioPHP's project biophp.org
 * Created 26 february 2019
 * Last modified 26 february 2019
 * @Todo : à refactoriser, créer une entité et le service appelle l'entité
 */
namespace MinitoolsBundle\Service;

class ReaderGffFastaManager
{
    //CONFIGURATION PARAMETERS
    public $INPUT_FILE;    //file input
    public $TYPE_FILE;     //type file
    public $ERROR;         // error in proccess
    public $OBJECT;        // content of file INPUT
    public $CURRENT;       // data of id current
    public $NUMBER_LINES;  //file number lines
    public $UID;           //list of ID unique
    public $NUMBER_UID;    //number of uniques elements
    private $READ_FILE=false;
    private $TYPE;


    public function getNumberLines()
    {
        return $this->NUMBER_LINES;
    }
    public function setNumberLines($numberLines)
    {
        $this->NUMBER_LINES = $numberLines;
    }


    public function getINPUT_FILE()
    {
        return $this->INPUT_FILE;
    }
    public function setINPUT_FILE($INPUT_FILE)
    {
        $this->INPUT_FILE = $INPUT_FILE;
        $this->CheckFileValid();
    }


    public function getTYPE_FILE()
    {
        return $this->TYPE_FILE;
    }
    public function setTYPE_FILE($TYPE_FILE)
    {
        $this->TYPE_FILE = $TYPE_FILE;
        $this->CheckTypeValid();
    }


    public function getERROR()
    {
        return $this->ERROR;
    }
    private  function setERROR($ERROR)
    {
        $this->ERROR .= $ERROR;
    }


    public function getUID()
    {
        return $this->UID;
    }
    private function setUID($UID)
    {
        $this->UID = $UID;
    }


    public function getNUMBER_UID()
    {
        return $this->NUMBER_UID;
    }
    private function setNUMBER_UID($NUMBER_UID) {
        $this->NUMBER_UID = $NUMBER_UID;
    }

    /**
     * check if file is valid
     * @return bool
     */
    private function CheckFileValid()
    {
        if(is_file($this->getINPUT_FILE())) {
            return true;
        } else {
            $this->setERROR("Invalid file ($this->getINPUT_FILE())!<br>");
            exit(1);
        }
    }


    /**
     * check if type is valid
     * @return bool
     */
    private function CheckTypeValid()
    {
        if(strtolower($this->getTYPE_FILE())=="gff" || strtolower($this->getTYPE_FILE())=="fasta") {
            $this->TYPE = strtolower($this->getTYPE_FILE());
            return true;
        } else {
            $this->setERROR("Invalid type ($this->getTYPE_FILE())!<br>");
            exit(1);
        }
    }


    /**
     * read file
     */
    public function read()
    {
        if ($this->CheckTypeValid() && $this->CheckFileValid()){
            if ($this->TYPE=="fasta"){
                $this->readFasta();
                $this->READ_FILE=true;
            }
            if ($this->TYPE=="gff"){
                $this->readGff();
                $this->READ_FILE=true;
            }
            $this->setUID(array_unique($this->UID));
            $this->setNUMBER_UID(count($this->getUID()));

        }
    }

    /**
     * reads Fasta
     */
    private function readFasta ()
    {
        $this->CheckTypeValid();
        $file = fopen($this->getINPUT_FILE(),"r");
        $contSeq = 0;
        $cont=-1;;

        while (!feof($file )) {
            $buffer = fgets($file);
            //read header
            if ($buffer[0] == ">") {
                $cont++;
                $aux = "";
                $all    = preg_split("/\s/",$buffer);
                $id     = str_replace(">","",$all[0]);
                $length = str_replace("length=","",$all[1]);
                $xy     = str_replace("xy=","",$all[2]);
                $region = str_replace("region=","",$all[3]);
                $run    = str_replace("run=","",$all[4]);
                $this->OBJECT[$cont] = new fasta();
                $this->OBJECT[$cont]->setId($id);
                $this->OBJECT[$cont]->setLength($length);
                $this->OBJECT[$cont]->setXy($xy);
                $this->OBJECT[$cont]->setRegion($region);
                $this->OBJECT[$cont]->setRun($run);
                $this->UID[]=$id;
                $contSeq++;
            } else {//read sequence
                $aux.=$buffer;
                $this->OBJECT[$cont]->setSequence($aux);
            }
        }
        $this->setNumberLines($contSeq);
    }


    /**
     * read type gff
     */
    private function readGff()
    {
        $this->CheckTypeValid();
        $file = fopen($this->getINPUT_FILE(),"r");
        $contSeq = 0;
        $cont = -1;

        while (!feof($file )) {
            $buffer = fgets($file);
            //read header
            if ($buffer[0]!="#") {
                $cont++;
                $aux = "";
                $all        = preg_split("/\t/",$buffer);
                $seqid      = $all[0];
                if ($seqid != "") {
                    $source     = $all[1];
                    $type       = $all[2];
                    $start      = $all[3];
                    $end        = $all[4];
                    $score      = $all[5];
                    $strand     = $all[6];
                    $phase      = $all[7];
                    $attributes = $all[8];

                    $this->OBJECT[$cont] = new gff();
                    $this->OBJECT[$cont]->setSeqid($seqid);
                    $this->OBJECT[$cont]->setSource($source);
                    $this->OBJECT[$cont]->setType($type);
                    $this->OBJECT[$cont]->setStart($start);
                    $this->OBJECT[$cont]->setEnd($end);
                    $this->OBJECT[$cont]->setScore($score);
                    $this->OBJECT[$cont]->setStrand($strand);
                    $this->OBJECT[$cont]->setPhase($phase);
                    $this->OBJECT[$cont]->setAttributes($attributes);
                    $this->UID[]=$seqid;

                    $contSeq++;
                }
            }

        }
        $this->setNumberLines($contSeq);
    }


    /**
     * Return data
     * @param $id
     * @todo : gérer les exceptions
     */
    function Find($id)
    {
        if($this->READ_FILE) {
            if($this->TYPE == "fasta") {
                for($i=0; $i<$this->getNumberLines(); $i++) {
                    if(trim($this->OBJECT[$i]->getId()) == trim($id)) {
                        $this->CURRENT = $this->OBJECT[$i];
                        $found = true;
                        break;
                    }
                }
            }

            if ($this->TYPE == "gff"){
                for ($i=0; $i < $this->getNumberLines(); $i++) {
                    if (trim($this->OBJECT[$i]->getSeqid()) == trim($id)) {
                        $this->CURRENT = $this->OBJECT[$i];
                        $found = true;
                        break;
                    }
                }
            }
            if(!$found) {
                echo "<br> id ($id) not found! $this->ERROR </br>";
            }
        } else {
            $this->setERROR("File not read");
            exit(1);
        }
    }
}