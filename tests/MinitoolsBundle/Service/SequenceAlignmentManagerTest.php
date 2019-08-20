<?php

namespace Tests\MinitoolsBundle\Service;

use PHPUnit\Framework\TestCase;
use MinitoolsBundle\Service\SequenceAlignmentManager;

class SequenceAlignmentManagerTest extends TestCase
{
    protected $matrix;

    public function setUp()
    {
        $this->matrix = [
              "AA" => 2,
              "AC" => -2,
              "AD" => 0,
              "AE" => 0,
              "AF" => -4,
              "AG" => 1,
              "AH" => -1,
              "AI" => -1,
              "AK" => -1,
              "AL" => -2,
              "AM" => -1,
              "AN" => 0,
              "AP" => 1,
              "AQ" => 0,
              "AR" => -2,
              "AS" => 1,
              "AT" => 1,
              "AV" => 0,
              "AW" => -6,
              "AY" => -3,
              "CA" => -2,
              "CC" => 12,
              "CD" => -5,
              "CE" => -5,
              "CF" => -4,
              "CG" => -3,
              "CH" => -3,
              "CI" => -2,
              "CK" => -5,
              "CL" => -6,
              "CM" => -5,
              "CN" => -4,
              "CP" => -3,
              "CQ" => -5,
              "CR" => -4,
              "CS" => 0,
              "CT" => -2,
              "CV" => -2,
              "CW" => -8,
              "CY" => 0,
              "DA" => 0,
              "DC" => -5,
              "DD" => 4,
              "DE" => 3,
              "DF" => -6,
              "DG" => 1,
              "DH" => 1,
              "DI" => -2,
              "DK" => 0,
              "DL" => -4,
              "DM" => -3,
              "DN" => 2,
              "DP" => -1,
              "DQ" => 2,
              "DR" => -1,
              "DS" => 0,
              "DT" => 0,
              "DV" => -2,
              "DW" => -7,
              "DY" => -4,
              "EA" => 0,
              "EC" => -5,
              "ED" => 3,
              "EE" => 4,
              "EF" => -5,
              "EG" => 0,
              "EH" => 1,
              "EI" => -2,
              "EK" => 0,
              "EL" => -3,
              "EM" => -2,
              "EN" => 1,
              "EP" => -1,
              "EQ" => 2,
              "ER" => -1,
              "ES" => 0,
              "ET" => 0,
              "EV" => -2,
              "EW" => -7,
              "EY" => -4,
              "FA" => -4,
              "FC" => -4,
              "FD" => -6,
              "FE" => -5,
              "FF" => 9,
              "FG" => -5,
              "FH" => -2,
              "FI" => 1,
              "FK" => -5,
              "FL" => 2,
              "FM" => 0,
              "FN" => -4,
              "FP" => -5,
              "FQ" => -5,
              "FR" => -4,
              "FS" => -3,
              "FT" => -3,
              "FV" => -1,
              "FW" => 0,
              "FY" => 7,
              "GA" => 1,
              "GC" => -3,
              "GD" => 1,
              "GE" => 0,
              "GF" => -5,
              "GG" => 5,
              "GH" => -2,
              "GI" => -3,
              "GK" => -2,
              "GL" => -4,
              "GM" => -3,
              "GN" => 0,
              "GP" => -1,
              "GQ" => -1,
              "GR" => -3,
              "GS" => 1,
              "GT" => 0,
              "GV" => -1,
              "GW" => -7,
              "GY" => -5,
              "HA" => -1,
              "HC" => -3,
              "HD" => 1,
              "HE" => 1,
              "HF" => -2,
              "HG" => -2,
              "HH" => 6,
              "HI" => -2,
              "HK" => 0,
              "HL" => -2,
              "HM" => -2,
              "HN" => 2,
              "HP" => 0,
              "HQ" => 3,
              "HR" => 2,
              "HS" => -1,
              "HT" => -1,
              "HV" => -2,
              "HW" => 3,
              "HY" => 0,
              "IA" => -1,
              "IC" => -2,
              "ID" => -2,
              "IE" => -2,
              "IF" => 1,
              "IG" => -3,
              "IH" => -2,
              "II" => 5,
              "IK" => -2,
              "IL" => 2,
              "IM" => 2,
              "IN" => -2,
              "IP" => -2,
              "IQ" => -2,
              "IR" => -2,
              "IS" => -1,
              "IT" => 0,
              "IV" => 4,
              "IW" => -5,
              "IY" => -1,
              "KA" => -1,
              "KC" => -5,
              "KD" => 0,
              "KE" => 0,
              "KF" => -5,
              "KG" => -2,
              "KH" => 0,
              "KI" => -2,
              "KK" => 5,
              "KL" => -3,
              "KM" => 0,
              "KN" => 1,
              "KP" => -1,
              "KQ" => 1,
              "KR" => 3,
              "KS" => 0,
              "KT" => 0,
              "KV" => -2,
              "KW" => -3,
              "KY" => -4,
              "LA" => -2,
              "LC" => -6,
              "LD" => -4,
              "LE" => -3,
              "LF" => 2,
              "LG" => -4,
              "LH" => -2,
              "LI" => 2,
              "LK" => -3,
              "LL" => 6,
              "LM" => 4,
              "LN" => -3,
              "LP" => -3,
              "LQ" => -2,
              "LR" => -3,
              "LS" => -3,
              "LT" => -2,
              "LV" => 2,
              "LW" => -2,
              "LY" => -1,
              "MA" => -1,
              "MC" => -5,
              "MD" => -3,
              "ME" => -2,
              "MF" => 0,
              "MG" => -3,
              "MH" => -2,
              "MI" => 2,
              "MK" => 0,
              "ML" => 4,
              "MM" => 6,
              "MN" => -2,
              "MP" => -2,
              "MQ" => -1,
              "MR" => 0,
              "MS" => -2,
              "MT" => -1,
              "MV" => 2,
              "MW" => -4,
              "MY" => -2,
              "NA" => 0,
              "NC" => -4,
              "ND" => 2,
              "NE" => 1,
              "NF" => -4,
              "NG" => 0,
              "NH" => 2,
              "NI" => -2,
              "NK" => 1,
              "NL" => -3,
              "NM" => -2,
              "NN" => 2,
              "NP" => -1,
              "NQ" => 1,
              "NR" => 0,
              "NS" => 1,
              "NT" => 0,
              "NV" => -2,
              "NW" => -4,
              "NY" => -2,
              "PA" => 1,
              "PC" => -3,
              "PD" => -1,
              "PE" => -1,
              "PF" => -5,
              "PG" => -1,
              "PH" => 0,
              "PI" => -2,
              "PK" => -1,
              "PL" => -3,
              "PM" => -2,
              "PN" => -1,
              "PP" => 6,
              "PQ" => 0,
              "PR" => 0,
              "PS" => 1,
              "PT" => 0,
              "PV" => -1,
              "PW" => -6,
              "PY" => -5,
              "QA" => 0,
              "QC" => -5,
              "QD" => 2,
              "QE" => 2,
              "QF" => -5,
              "QG" => -1,
              "QH" => 3,
              "QI" => -2,
              "QK" => 1,
              "QL" => -2,
              "QM" => -1,
              "QN" => 1,
              "QP" => 0,
              "QQ" => 4,
              "QR" => 1,
              "QS" => -1,
              "QT" => -1,
              "QV" => -2,
              "QW" => -5,
              "QY" => -4,
              "RA" => -2,
              "RC" => -4,
              "RD" => -1,
              "RE" => -1,
              "RF" => -4,
              "RG" => -3,
              "RH" => 2,
              "RI" => -2,
              "RK" => 3,
              "RL" => -3,
              "RM" => 0,
              "RN" => 0,
              "RP" => 0,
              "RQ" => 1,
              "RR" => 6,
              "RS" => 0,
              "RT" => -1,
              "RV" => -2,
              "RW" => 2,
              "RY" => -4,
              "SA" => 1,
              "SC" => 0,
              "SD" => 0,
              "SE" => 0,
              "SF" => -3,
              "SG" => 1,
              "SH" => -1,
              "SI" => -1,
              "SK" => 0,
              "SL" => -3,
              "SM" => -2,
              "SN" => 1,
              "SP" => 1,
              "SQ" => -1,
              "SR" => 0,
              "SS" => 2,
              "ST" => 1,
              "SV" => -1,
              "SW" => -2,
              "SY" => -3,
              "TA" => 1,
              "TC" => -2,
              "TD" => 0,
              "TE" => 0,
              "TF" => -3,
              "TG" => 0,
              "TH" => -1,
              "TI" => 0,
              "TK" => 0,
              "TL" => -2,
              "TM" => -1,
              "TN" => 0,
              "TP" => 0,
              "TQ" => -1,
              "TR" => -1,
              "TS" => 1,
              "TT" => 3,
              "TV" => 0,
              "TW" => -5,
              "TY" => -3,
              "VA" => 0,
              "VC" => -2,
              "VD" => -2,
              "VE" => -2,
              "VF" => -1,
              "VG" => -1,
              "VH" => -2,
              "VI" => 4,
              "VK" => -2,
              "VL" => 2,
              "VM" => 2,
              "VN" => -2,
              "VP" => -1,
              "VQ" => -2,
              "VR" => -2,
              "VS" => -1,
              "VT" => 0,
              "VV" => 4,
              "VW" => -6,
              "VY" => -2,
              "WA" => -6,
              "WC" => -8,
              "WD" => -7,
              "WE" => -7,
              "WF" => 0,
              "WG" => -7,
              "WH" => 3,
              "WI" => -5,
              "WK" => -3,
              "WL" => -2,
              "WM" => -4,
              "WN" => -4,
              "WP" => -6,
              "WQ" => -5,
              "WR" => 2,
              "WS" => -2,
              "WT" => -5,
              "WV" => -6,
              "WW" => 17,
              "WY" => 0,
              "YA" => -3,
              "YC" => 0,
              "YD" => -4,
              "YE" => -4,
              "YF" => 7,
              "YG" => -5,
              "YH" => 0,
              "YI" => -1,
              "YK" => -4,
              "YL" => -1,
              "YM" => -2,
              "YN" => -2,
              "YP" => -5,
              "YQ" => -4,
              "YR" => -4,
              "YS" => -3,
              "YT" => -3,
              "YV" => -2,
              "YW" => 0,
              "YY" => 10,
            ];

        /**
         * Mock API
         */
        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')->getMock();
        $serializerMock = $this->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiMock = $this->getMockBuilder('AppBundle\Bioapi\Bioapi')
            ->setConstructorArgs([$clientMock, $serializerMock])
            ->setMethods(["getPam250Matrix"])
            ->getMock();
        $this->apiMock->method("getPam250Matrix")->will($this->returnValue($this->matrix));
    }

    public function testAlignDNA()
    {
        $seqa = "GGAGTGAGGGGAGCAGTTGGCTGAAGATGGTCCCCGCCGAGGGACCGGTGGGCGACGGCGAGCTGTGGCAGACCTGGCTTCCTAACCACGTCCGTGTTCTTGCGGCTCCGGGAGGGACTG";
        $seqb = "CGCATGCGGAGTGAGGGGAGCAGTTGGGAACAGATGGTCCCCGCCGAGGGACCGGTGGGCGACGGCCAGCTGTGGCAGACCTGGCTTCCTAACCACGGAACGTTCTTTCCGCTCCGGGAG";

        $aExpected = [
          "seqa" => "-------GGAGTGAGGGGAGCAGTTGGCTGAAGATGGTCCCCGCCGAGGGACCGGTGGGCGACGGCGAGCTGTGGCAGACCTGGCTTCCTAACCACGTC-CGTGTTCTTGCGGCTCCGGGAGGGACT",
          "seqb" => "CGCATGCGGAGTGAGGGGAGCAGTTGGGAACAGATGGTCCCCGCCGAGGGACCGGTGGGCGACGGCCAGCTGTGGCAGACCTGGCTTCCTAACCACGGAACGT--TCTTTCCGCTCCGGGAG-----"
        ];

        $service = new SequenceAlignmentManager($this->apiMock);
        $testFunction = $service->alignDNA($seqa, $seqb);

        $this->assertEquals($aExpected, $testFunction);
    }
}