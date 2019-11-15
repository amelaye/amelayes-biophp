<?php

namespace Tests\MinitoolsBundle\Service;

use AppBundle\Service\Misc\OligosManager;
use MinitoolsBundle\Service\SkewsManager;
use PHPUnit\Framework\TestCase;

class SkewsManagerTest extends TestCase
{
    protected $oligosManager;

    public function setUp()
    {
        /**
         * Mock API
         */
        $value = [
          "T" => "A",
          "G" => "C",
          "C" => "G",
          "A" => "T",
        ];
        $clientMock = $this->getMockBuilder('GuzzleHttp\Client')->getMock();
        $serializerMock = $this->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiMock = $this->getMockBuilder('AppBundle\Bioapi\Bioapi')
            ->setConstructorArgs([$clientMock, $serializerMock])
            ->setMethods(["getDNAComplement"])
            ->getMock();
        $this->apiMock->method("getDNAComplement")->will($this->returnValue($value));

        $this->oligosManager = new OligosManager($this->apiMock);


    }

    public function testOligoSkewArrayCalculationOneStrand()
    {
        $sequence = "GGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGG";
        $window = "100";
        $oskew = 2;
        $strands = 1;
        $aExpected = [
          0 => 0.01022312,
          10 => 0.00447745,
          20 => 0.00869665,
          30 => 0.00353055,
          40 => 0.00252984,
          50 => 0.0030252,
          60 => 0.00313104,
          70 => 0.00220797,
          80 => 0.00647436,
          90 => 0.00159728,
          100 => 0.00252984,
          110 => 0.00315182,
          120 => 0.01022312,
          130 => 0.00447745,
          140 => 0.00869665,
          150 => 0.00353055,
          160 => 0.00252984,
          170 => 0.0030252,
          180 => 0.00313104,
          190 => 0.00220797,
          200 => 0.00647436,
          210 => 0.00159728,
          220 => 0.00252984,
          230 => 0.00315182,
          240 => 0.01022312,
          250 => 0.00447745,
          260 => 0.00869665,
          270 => 0.00353055,
          280 => 0.00252984,
          290 => 0.0030252,
          300 => 0.00313104,
          310 => 0.00220797,
          320 => 0.00647436,
          330 => 0.00159728,
          340 => 0.00252984,
          350 => 0.00315182,
          360 => 0.01022312,
          370 => 0.00447745,
          380 => 0.00869665,
          390 => 0.00353055,
          400 => 0.00252984,
          410 => 0.0030252,
          420 => 0.00313104,
          430 => 0.00220797,
          440 => 0.00647436,
          450 => 0.00159728,
          460 => 0.00252984,
          470 => 0.00315182,
          480 => 0.01022312,
          490 => 0.00447745,
          500 => 0.00869665,
          510 => 0.00353055,
          520 => 0.00252984,
          530 => 0.0030252,
          540 => 0.00313104,
          550 => 0.00220797,
          560 => 0.00647436,
          570 => 0.00159728,
          580 => 0.00252984,
          590 => 0.00315182,
          600 => 0.01022312,
          610 => 0.00447745,
          620 => 0.00869665,
          630 => 0.00353055,
          640 => 0.00252984,
          650 => 0.0030252,
          660 => 0.00313104,
          670 => 0.00220797,
          680 => 0.00647436,
          690 => 0.00159728,
          700 => 0.00252984,
          710 => 0.00315182,
          720 => 0.01022312,
          730 => 0.00447745,
          740 => 0.00869665,
          750 => 0.00353055,
          760 => 0.00252984,
          770 => 0.0030252,
          780 => 0.00313104,
          790 => 0.00220797,
          800 => 0.00647436,
          810 => 0.00159728,
          820 => 0.00252984,
          830 => 0.00315182,
          840 => 0.01022312,
          850 => 0.00447745,
          860 => 0.00869665,
          870 => 0.00353055,
          880 => 0.00252984,
          890 => 0.0030252,
          900 => 0.00313104,
          910 => 0.00220797,
          920 => 0.00647436,
          930 => 0.00159728,
          940 => 0.00252984,
          950 => 0.00315182,
          960 => 0.01022312,
          970 => 0.00447745,
          980 => 0.00869665,
          990 => 0.00353055,
          1000 => 0.00252984,
          1010 => 0.0030252,
          1020 => 0.00313104,
          1030 => 0.00220797,
          1040 => 0.00647436,
          1050 => 0.00159728,
          1060 => 0.00252984,
          1070 => 0.00315182,
          1080 => 0.01022312,
          1090 => 0.00447745,
          1100 => 0.00869665,
          1110 => 0.00353055,
          1120 => 0.00252984,
          1130 => 0.0030252,
          1140 => 0.00313104,
          1150 => 0.00220797,
          1160 => 0.00647436,
          1170 => 0.00159728,
          1180 => 0.00252984,
          1190 => 0.00315182,
          1200 => 0.01022312,
          1210 => 0.00447745,
          1220 => 0.00869665,
          1230 => 0.00353055,
          1240 => 0.00252984,
          1250 => 0.0030252,
          1260 => 0.00313104,
          1270 => 0.00220797,
          1280 => 0.00647436,
          1290 => 0.00159728,
          1300 => 0.00252984,
          1310 => 0.00315182,
          1320 => 0.01022312,
          1330 => 0.00447745,
          1340 => 0.00869665,
          1350 => 0.00353055,
          1360 => 0.00252984,
          1370 => 0.0030252,
          1380 => 0.00313104,
          1390 => 0.00220797,
          1400 => 0.00647436,
        ];

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->oligoSkewArrayCalculation($sequence, $window, $oskew, $strands);

        $this->assertEquals($aExpected, $testFunction);
    }

    public function testOligoSkewArrayCalculationTwoStrands()
    {
        $sequence = "GGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGG";
        $window = "100";
        $oskew = 2;
        $strands = 2;
        $aExpected = [
          0 => 0.32147015,
          10 => 0.33096696,
          20 => 0.3510755,
          30 => 0.32194983,
          40 => 0.27795001,
          50 => 0.27264332,
          60 => 0.28485569,
          70 => 0.29293218,
          80 => 0.33283053,
          90 => 0.3056786,
          100 => 0.27795001,
          110 => 0.28376523,
          120 => 0.32147015,
          130 => 0.33096696,
          140 => 0.3510755,
          150 => 0.32194983,
          160 => 0.27795001,
          170 => 0.27264332,
          180 => 0.28485569,
          190 => 0.29293218,
          200 => 0.33283053,
          210 => 0.3056786,
          220 => 0.27795001,
          230 => 0.28376523,
          240 => 0.32147015,
          250 => 0.33096696,
          260 => 0.3510755,
          270 => 0.32194983,
          280 => 0.27795001,
          290 => 0.27264332,
          300 => 0.28485569,
          310 => 0.29293218,
          320 => 0.33283053,
          330 => 0.3056786,
          340 => 0.27795001,
          350 => 0.28376523,
          360 => 0.32147015,
          370 => 0.33096696,
          380 => 0.3510755,
          390 => 0.32194983,
          400 => 0.27795001,
          410 => 0.27264332,
          420 => 0.28485569,
          430 => 0.29293218,
          440 => 0.33283053,
          450 => 0.3056786,
          460 => 0.27795001,
          470 => 0.28376523,
          480 => 0.32147015,
          490 => 0.33096696,
          500 => 0.3510755,
          510 => 0.32194983,
          520 => 0.27795001,
          530 => 0.27264332,
          540 => 0.28485569,
          550 => 0.29293218,
          560 => 0.33283053,
          570 => 0.3056786,
          580 => 0.27795001,
          590 => 0.28376523,
          600 => 0.32147015,
          610 => 0.33096696,
          620 => 0.3510755,
          630 => 0.32194983,
          640 => 0.27795001,
          650 => 0.27264332,
          660 => 0.28485569,
          670 => 0.29293218,
          680 => 0.33283053,
          690 => 0.3056786,
          700 => 0.27795001,
          710 => 0.28376523,
          720 => 0.32147015,
          730 => 0.33096696,
          740 => 0.3510755,
          750 => 0.32194983,
          760 => 0.27795001,
          770 => 0.27264332,
          780 => 0.28485569,
          790 => 0.29293218,
          800 => 0.33283053,
          810 => 0.3056786,
          820 => 0.27795001,
          830 => 0.28376523,
          840 => 0.32147015,
          850 => 0.33096696,
          860 => 0.3510755,
          870 => 0.32194983,
          880 => 0.27795001,
          890 => 0.27264332,
          900 => 0.28485569,
          910 => 0.29293218,
          920 => 0.33283053,
          930 => 0.3056786,
          940 => 0.27795001,
          950 => 0.28376523,
          960 => 0.32147015,
          970 => 0.33096696,
          980 => 0.3510755,
          990 => 0.32194983,
          1000 => 0.27795001,
          1010 => 0.27264332,
          1020 => 0.28485569,
          1030 => 0.29293218,
          1040 => 0.33283053,
          1050 => 0.3056786,
          1060 => 0.27795001,
          1070 => 0.28376523,
          1080 => 0.32147015,
          1090 => 0.33096696,
          1100 => 0.3510755,
          1110 => 0.32194983,
          1120 => 0.27795001,
          1130 => 0.27264332,
          1140 => 0.28485569,
          1150 => 0.29293218,
          1160 => 0.33283053,
          1170 => 0.3056786,
          1180 => 0.27795001,
          1190 => 0.28376523,
          1200 => 0.32147015,
          1210 => 0.33096696,
          1220 => 0.3510755,
          1230 => 0.32194983,
          1240 => 0.27795001,
          1250 => 0.27264332,
          1260 => 0.28485569,
          1270 => 0.29293218,
          1280 => 0.33283053,
          1290 => 0.3056786,
          1300 => 0.27795001,
          1310 => 0.28376523,
          1320 => 0.32147015,
          1330 => 0.33096696,
          1340 => 0.3510755,
          1350 => 0.32194983,
          1360 => 0.27795001,
          1370 => 0.27264332,
          1380 => 0.28485569,
          1390 => 0.29293218,
          1400 => 0.33283053,
        ];

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->oligoSkewArrayCalculation($sequence, $window, $oskew, $strands);

        $this->assertEquals($aExpected, $testFunction);
    }

    public function testDistance()
    {
        $vals_x = [
          "AAAA" => 0,
          "AAAC" => 0,
          "AAAG" => 0,
          "AAAT" => 0,
          "AACA" => 0,
          "AACC" => 0,
          "AACG" => 0,
          "AACT" => 0,
          "AAGA" => 25,
          "AAGC" => 0,
          "AAGG" => 0,
          "AAGT" => 0,
          "AATA" => 0,
          "AATC" => 0,
          "AATG" => 0,
          "AATT" => 0,
          "ACAA" => 0,
          "ACAC" => 0,
          "ACAG" => 0,
          "ACAT" => 0,
          "ACCA" => 0,
          "ACCC" => 0,
          "ACCG" => 25,
          "ACCT" => 0,
          "ACGA" => 0,
          "ACGC" => 13,
          "ACGG" => 12,
          "ACGT" => 0,
          "ACTA" => 0,
          "ACTC" => 0,
          "ACTG" => 0,
          "ACTT" => 0,
          "AGAA" => 0,
          "AGAC" => 0,
          "AGAG" => 0,
          "AGAT" => 25,
          "AGCA" => 25,
          "AGCC" => 0,
          "AGCG" => 0,
          "AGCT" => 0,
          "AGGA" => 0,
          "AGGC" => 0,
          "AGGG" => 50,
          "AGGT" => 0,
          "AGTA" => 0,
          "AGTC" => 0,
          "AGTG" => 25,
          "AGTT" => 25,
          "ATAA" => 0,
          "ATAC" => 0,
          "ATAG" => 0,
          "ATAT" => 0,
          "ATCA" => 0,
          "ATCC" => 0,
          "ATCG" => 0,
          "ATCT" => 0,
          "ATGA" => 0,
          "ATGC" => 0,
          "ATGG" => 25,
          "ATGT" => 0,
          "ATTA" => 0,
          "ATTC" => 0,
          "ATTG" => 0,
          "ATTT" => 0,
          "CAAA" => 0,
          "CAAC" => 0,
          "CAAG" => 25,
          "CAAT" => 0,
          "CACA" => 0,
          "CACC" => 0,
          "CACG" => 0,
          "CACT" => 0,
          "CAGA" => 0,
          "CAGC" => 0,
          "CAGG" => 0,
          "CAGT" => 25,
          "CATA" => 0,
          "CATC" => 0,
          "CATG" => 0,
          "CATT" => 0,
          "CCAA" => 25,
          "CCAC" => 0,
          "CCAG" => 0,
          "CCAT" => 0,
          "CCCA" => 0,
          "CCCC" => 0,
          "CCCG" => 0,
          "CCCT" => 0,
          "CCGA" => 25,
          "CCGC" => 25,
          "CCGG" => 25,
          "CCGT" => 0,
          "CCTA" => 0,
          "CCTC" => 0,
          "CCTG" => 0,
          "CCTT" => 0,
          "CGAA" => 0,
          "CGAC" => 25,
          "CGAG" => 25,
          "CGAT" => 0,
          "CGCA" => 0,
          "CGCC" => 25,
          "CGCG" => 13,
          "CGCT" => 0,
          "CGGA" => 0,
          "CGGC" => 25,
          "CGGG" => 24,
          "CGGT" => 25,
          "CGTA" => 0,
          "CGTC" => 0,
          "CGTG" => 0,
          "CGTT" => 0,
          "CTAA" => 0,
          "CTAC" => 0,
          "CTAG" => 0,
          "CTAT" => 0,
          "CTCA" => 0,
          "CTCC" => 0,
          "CTCG" => 0,
          "CTCT" => 0,
          "CTGA" => 0,
          "CTGC" => 0,
          "CTGG" => 0,
          "CTGT" => 0,
          "CTTA" => 0,
          "CTTC" => 0,
          "CTTG" => 0,
          "CTTT" => 0,
          "GAAA" => 0,
          "GAAC" => 0,
          "GAAG" => 0,
          "GAAT" => 0,
          "GACA" => 0,
          "GACC" => 25,
          "GACG" => 25,
          "GACT" => 0,
          "GAGA" => 0,
          "GAGC" => 25,
          "GAGG" => 50,
          "GAGT" => 25,
          "GATA" => 0,
          "GATC" => 0,
          "GATG" => 25,
          "GATT" => 0,
          "GCAA" => 0,
          "GCAC" => 0,
          "GCAG" => 25,
          "GCAT" => 0,
          "GCCA" => 25,
          "GCCC" => 0,
          "GCCG" => 50,
          "GCCT" => 0,
          "GCGA" => 25,
          "GCGC" => 0,
          "GCGG" => 38,
          "GCGT" => 0,
          "GCTA" => 0,
          "GCTC" => 0,
          "GCTG" => 0,
          "GCTT" => 0,
          "GGAA" => 0,
          "GGAC" => 25,
          "GGAG" => 50,
          "GGAT" => 0,
          "GGCA" => 0,
          "GGCC" => 50,
          "GGCG" => 50,
          "GGCT" => 0,
          "GGGA" => 74,
          "GGGC" => 50,
          "GGGG" => 73,
          "GGGT" => 0,
          "GGTA" => 0,
          "GGTC" => 0,
          "GGTG" => 25,
          "GGTT" => 0,
          "GTAA" => 0,
          "GTAC" => 0,
          "GTAG" => 0,
          "GTAT" => 0,
          "GTCA" => 0,
          "GTCC" => 0,
          "GTCG" => 0,
          "GTCT" => 0,
          "GTGA" => 25,
          "GTGC" => 0,
          "GTGG" => 25,
          "GTGT" => 0,
          "GTTA" => 0,
          "GTTC" => 0,
          "GTTG" => 25,
          "GTTT" => 0,
          "TAAA" => 0,
          "TAAC" => 0,
          "TAAG" => 0,
          "TAAT" => 0,
          "TACA" => 0,
          "TACC" => 0,
          "TACG" => 0,
          "TACT" => 0,
          "TAGA" => 0,
          "TAGC" => 0,
          "TAGG" => 0,
          "TAGT" => 0,
          "TATA" => 0,
          "TATC" => 0,
          "TATG" => 0,
          "TATT" => 0,
          "TCAA" => 0,
          "TCAC" => 0,
          "TCAG" => 0,
          "TCAT" => 0,
          "TCCA" => 0,
          "TCCC" => 0,
          "TCCG" => 0,
          "TCCT" => 0,
          "TCGA" => 0,
          "TCGC" => 0,
          "TCGG" => 0,
          "TCGT" => 0,
          "TCTA" => 0,
          "TCTC" => 0,
          "TCTG" => 0,
          "TCTT" => 0,
          "TGAA" => 0,
          "TGAC" => 0,
          "TGAG" => 25,
          "TGAT" => 0,
          "TGCA" => 0,
          "TGCC" => 0,
          "TGCG" => 0,
          "TGCT" => 0,
          "TGGA" => 0,
          "TGGC" => 25,
          "TGGG" => 50,
          "TGGT" => 0,
          "TGTA" => 0,
          "TGTC" => 0,
          "TGTG" => 0,
          "TGTT" => 0,
          "TTAA" => 0,
          "TTAC" => 0,
          "TTAG" => 0,
          "TTAT" => 0,
          "TTCA" => 0,
          "TTCC" => 0,
          "TTCG" => 0,
          "TTCT" => 0,
          "TTGA" => 0,
          "TTGC" => 0,
          "TTGG" => 25,
          "TTGT" => 0,
          "TTTA" => 0,
          "TTTC" => 0,
          "TTTG" => 0,
          "TTTT" => 0,
        ];

        $vals_y = [
          "AAAA" => 0,
          "AAAC" => 0,
          "AAAG" => 0,
          "AAAT" => 0,
          "AACA" => 0,
          "AACC" => 0,
          "AACG" => 0,
          "AACT" => 2,
          "AAGA" => 2,
          "AAGC" => 0,
          "AAGG" => 0,
          "AAGT" => 0,
          "AATA" => 0,
          "AATC" => 0,
          "AATG" => 0,
          "AATT" => 0,
          "ACAA" => 0,
          "ACAC" => 0,
          "ACAG" => 0,
          "ACAT" => 0,
          "ACCA" => 0,
          "ACCC" => 0,
          "ACCG" => 2,
          "ACCT" => 0,
          "ACGA" => 0,
          "ACGC" => 1,
          "ACGG" => 0,
          "ACGT" => 0,
          "ACTA" => 0,
          "ACTC" => 2,
          "ACTG" => 2,
          "ACTT" => 0,
          "AGAA" => 0,
          "AGAC" => 0,
          "AGAG" => 0,
          "AGAT" => 2,
          "AGCA" => 2,
          "AGCC" => 0,
          "AGCG" => 0,
          "AGCT" => 0,
          "AGGA" => 0,
          "AGGC" => 0,
          "AGGG" => 3,
          "AGGT" => 0,
          "AGTA" => 0,
          "AGTC" => 0,
          "AGTG" => 2,
          "AGTT" => 2,
          "ATAA" => 0,
          "ATAC" => 0,
          "ATAG" => 0,
          "ATAT" => 0,
          "ATCA" => 0,
          "ATCC" => 0,
          "ATCG" => 0,
          "ATCT" => 2,
          "ATGA" => 0,
          "ATGC" => 0,
          "ATGG" => 2,
          "ATGT" => 0,
          "ATTA" => 0,
          "ATTC" => 0,
          "ATTG" => 0,
          "ATTT" => 0,
          "CAAA" => 0,
          "CAAC" => 2,
          "CAAG" => 2,
          "CAAT" => 0,
          "CACA" => 0,
          "CACC" => 1,
          "CACG" => 0,
          "CACT" => 2,
          "CAGA" => 0,
          "CAGC" => 0,
          "CAGG" => 0,
          "CAGT" => 2,
          "CATA" => 0,
          "CATC" => 2,
          "CATG" => 0,
          "CATT" => 0,
          "CCAA" => 4,
          "CCAC" => 1,
          "CCAG" => 0,
          "CCAT" => 2,
          "CCCA" => 3,
          "CCCC" => 2,
          "CCCG" => 1,
          "CCCT" => 3,
          "CCGA" => 2,
          "CCGC" => 5,
          "CCGG" => 2,
          "CCGT" => 0,
          "CCTA" => 0,
          "CCTC" => 3,
          "CCTG" => 0,
          "CCTT" => 0,
          "CGAA" => 0,
          "CGAC" => 1,
          "CGAG" => 2,
          "CGAT" => 0,
          "CGCA" => 0,
          "CGCC" => 5,
          "CGCG" => 2,
          "CGCT" => 0,
          "CGGA" => 0,
          "CGGC" => 6,
          "CGGG" => 1,
          "CGGT" => 2,
          "CGTA" => 0,
          "CGTC" => 1,
          "CGTG" => 0,
          "CGTT" => 0,
          "CTAA" => 0,
          "CTAC" => 0,
          "CTAG" => 0,
          "CTAT" => 0,
          "CTCA" => 2,
          "CTCC" => 4,
          "CTCG" => 2,
          "CTCT" => 0,
          "CTGA" => 0,
          "CTGC" => 2,
          "CTGG" => 0,
          "CTGT" => 0,
          "CTTA" => 0,
          "CTTC" => 0,
          "CTTG" => 2,
          "CTTT" => 0,
          "GAAA" => 0,
          "GAAC" => 0,
          "GAAG" => 0,
          "GAAT" => 0,
          "GACA" => 0,
          "GACC" => 1,
          "GACG" => 1,
          "GACT" => 0,
          "GAGA" => 0,
          "GAGC" => 2,
          "GAGG" => 3,
          "GAGT" => 2,
          "GATA" => 0,
          "GATC" => 0,
          "GATG" => 2,
          "GATT" => 0,
          "GCAA" => 0,
          "GCAC" => 0,
          "GCAG" => 2,
          "GCAT" => 0,
          "GCCA" => 4,
          "GCCC" => 3,
          "GCCG" => 6,
          "GCCT" => 0,
          "GCGA" => 1,
          "GCGC" => 0,
          "GCGG" => 5,
          "GCGT" => 1,
          "GCTA" => 0,
          "GCTC" => 2,
          "GCTG" => 0,
          "GCTT" => 0,
          "GGAA" => 0,
          "GGAC" => 1,
          "GGAG" => 4,
          "GGAT" => 0,
          "GGCA" => 0,
          "GGCC" => 8,
          "GGCG" => 5,
          "GGCT" => 0,
          "GGGA" => 4,
          "GGGC" => 3,
          "GGGG" => 2,
          "GGGT" => 0,
          "GGTA" => 0,
          "GGTC" => 1,
          "GGTG" => 1,
          "GGTT" => 0,
          "GTAA" => 0,
          "GTAC" => 0,
          "GTAG" => 0,
          "GTAT" => 0,
          "GTCA" => 0,
          "GTCC" => 1,
          "GTCG" => 1,
          "GTCT" => 0,
          "GTGA" => 2,
          "GTGC" => 0,
          "GTGG" => 1,
          "GTGT" => 0,
          "GTTA" => 0,
          "GTTC" => 0,
          "GTTG" => 2,
          "GTTT" => 0,
          "TAAA" => 0,
          "TAAC" => 0,
          "TAAG" => 0,
          "TAAT" => 0,
          "TACA" => 0,
          "TACC" => 0,
          "TACG" => 0,
          "TACT" => 0,
          "TAGA" => 0,
          "TAGC" => 0,
          "TAGG" => 0,
          "TAGT" => 0,
          "TATA" => 0,
          "TATC" => 0,
          "TATG" => 0,
          "TATT" => 0,
          "TCAA" => 0,
          "TCAC" => 2,
          "TCAG" => 0,
          "TCAT" => 0,
          "TCCA" => 0,
          "TCCC" => 4,
          "TCCG" => 0,
          "TCCT" => 0,
          "TCGA" => 0,
          "TCGC" => 1,
          "TCGG" => 2,
          "TCGT" => 0,
          "TCTA" => 0,
          "TCTC" => 0,
          "TCTG" => 0,
          "TCTT" => 2,
          "TGAA" => 0,
          "TGAC" => 0,
          "TGAG" => 2,
          "TGAT" => 0,
          "TGCA" => 0,
          "TGCC" => 0,
          "TGCG" => 0,
          "TGCT" => 2,
          "TGGA" => 0,
          "TGGC" => 4,
          "TGGG" => 3,
          "TGGT" => 0,
          "TGTA" => 0,
          "TGTC" => 0,
          "TGTG" => 0,
          "TGTT" => 0,
          "TTAA" => 0,
          "TTAC" => 0,
          "TTAG" => 0,
          "TTAT" => 0,
          "TTCA" => 0,
          "TTCC" => 0,
          "TTCG" => 0,
          "TTCT" => 0,
          "TTGA" => 0,
          "TTGC" => 0,
          "TTGG" => 4,
          "TTGT" => 0,
          "TTTA" => 0,
          "TTTC" => 0,
          "TTTG" => 0,
          "TTTT" => 0,
        ];

        $fExpected = 0.69309198;

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->distance($vals_x, $vals_y);

        $this->assertEquals($fExpected, $testFunction);
    }

    public function testComputeImageWithGmC()
    {
        $sequence = "GGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGG";
        $pos = 0;
        $window = 100;
        $AT = false;
        $KETO = false;
        $GmC = true;
        $len_seq = 1500;
        $period = 1.0;
        $dAT = [
          0 => null
        ];
        $dGC = [
            0 => null
        ];
        $dGmC = [
            0 => null
        ];
        $dKETO = [
            0 => null
        ];

        $fExpected = 0.52;

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->computeImage($sequence,$pos, $window, $AT, $KETO, $GmC, $len_seq, $period,$dAT,$dGC,$dGmC,$dKETO);

        $this->assertEquals($fExpected, $testFunction);
    }

    public function testComputeImageWithGmCATKETO()
    {
        $sequence = "GGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGG";
        $pos = 0;
        $window = 100;
        $AT = true;
        $KETO = true;
        $GmC = true;
        $len_seq = 1500;
        $period = 1.0;
        $dAT = [
            0 => null
        ];
        $dGC = [
            0 => null
        ];
        $dGmC = [
            0 => null
        ];
        $dKETO = [
            0 => null
        ];

        $fExpected = 0.56;

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->computeImage($sequence,$pos, $window, $AT, $KETO, $GmC, $len_seq, $period,$dAT,$dGC,$dGmC,$dKETO);

        $this->assertEquals($fExpected, $testFunction);
    }

    public function testCreateImage()
    {
        $sequence = "GGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGGGGGGGAGTGAGGGGAGCAGTTGGGCCAAGATGGCGGCCGCCGAGGGACCGGTGGGCGACGCGG";
        $window = 100;
        $GC = true;
        $AT = true;
        $KETO = true;
        $GmC = true;
        $oligo_skew_array = [
          0 => 0.69309198,
          10 => 0.71703971,
          20 => 0.74480352,
          30 => 0.41449359,
          40 => 0.14568539,
          50 => 0.20686472,
          60 => 0.49426386,
          70 => 0.60828411,
          80 => 0.59639906,
          90 => 0.29521861,
          100 => 0.17217171,
          110 => 0.23181336,
          120 => 0.69309198,
          130 => 0.71703971,
          140 => 0.74480352,
          150 => 0.41449359,
          160 => 0.14568539,
          170 => 0.20686472,
          180 => 0.49426386,
          190 => 0.60828411,
          200 => 0.59639906,
          210 => 0.29521861,
          220 => 0.17217171,
          230 => 0.23181336,
          240 => 0.69309198,
          250 => 0.71703971,
          260 => 0.74480352,
          270 => 0.41449359,
          280 => 0.14568539,
          290 => 0.20686472,
          300 => 0.49426386,
          310 => 0.60828411,
          320 => 0.59639906,
          330 => 0.29521861,
          340 => 0.17217171,
          350 => 0.23181336,
          360 => 0.69309198,
          370 => 0.71703971,
          380 => 0.74480352,
          390 => 0.41449359,
          400 => 0.14568539,
          410 => 0.20686472,
          420 => 0.49426386,
          430 => 0.60828411,
          440 => 0.59639906,
          450 => 0.29521861,
          460 => 0.17217171,
          470 => 0.23181336,
          480 => 0.69309198,
          490 => 0.71703971,
          500 => 0.74480352,
          510 => 0.41449359,
          520 => 0.14568539,
          530 => 0.20686472,
          540 => 0.49426386,
          550 => 0.60828411,
          560 => 0.59639906,
          570 => 0.29521861,
          580 => 0.17217171,
          590 => 0.23181336,
          600 => 0.69309198,
          610 => 0.71703971,
          620 => 0.74480352,
          630 => 0.41449359,
          640 => 0.14568539,
          650 => 0.20686472,
          660 => 0.49426386,
          670 => 0.60828411,
          680 => 0.59639906,
          690 => 0.29521861,
          700 => 0.17217171,
          710 => 0.23181336,
          720 => 0.69309198,
          730 => 0.71703971,
          740 => 0.74480352,
          750 => 0.41449359,
          760 => 0.14568539,
          770 => 0.20686472,
          780 => 0.49426386,
          790 => 0.60828411,
          800 => 0.59639906,
          810 => 0.29521861,
          820 => 0.17217171,
          830 => 0.23181336,
          840 => 0.69309198,
          850 => 0.71703971,
          860 => 0.74480352,
          870 => 0.41449359,
          880 => 0.14568539,
          890 => 0.20686472,
          900 => 0.49426386,
          910 => 0.60828411,
          920 => 0.59639906,
          930 => 0.29521861,
          940 => 0.17217171,
          950 => 0.23181336,
          960 => 0.69309198,
          970 => 0.71703971,
          980 => 0.74480352,
          990 => 0.41449359,
          1000 => 0.14568539,
          1010 => 0.20686472,
          1020 => 0.49426386,
          1030 => 0.60828411,
          1040 => 0.59639906,
          1050 => 0.29521861,
          1060 => 0.17217171,
          1070 => 0.23181336,
          1080 => 0.69309198,
          1090 => 0.71703971,
          1100 => 0.74480352,
          1110 => 0.41449359,
          1120 => 0.14568539,
          1130 => 0.20686472,
          1140 => 0.49426386,
          1150 => 0.60828411,
          1160 => 0.59639906,
          1170 => 0.29521861,
          1180 => 0.17217171,
          1190 => 0.23181336,
          1200 => 0.69309198,
          1210 => 0.71703971,
          1220 => 0.74480352,
          1230 => 0.41449359,
          1240 => 0.14568539,
          1250 => 0.20686472,
          1260 => 0.49426386,
          1270 => 0.60828411,
          1280 => 0.59639906,
          1290 => 0.29521861,
          1300 => 0.17217171,
          1310 => 0.23181336,
          1320 => 0.69309198,
          1330 => 0.71703971,
          1340 => 0.74480352,
          1350 => 0.41449359,
          1360 => 0.14568539,
          1370 => 0.20686472,
          1380 => 0.49426386,
          1390 => 0.60828411,
          1400 => 0.59639906,
        ];
        $olen = 4;
        $from = null;
        $to = null;
        $name = "prout";

        $sExpected = "prout.png";
        if(!is_dir("public"))
            mkdir("public");
        if(!is_dir("public/uploads"))
            mkdir("public/uploads");

        $service = new SkewsManager($this->oligosManager);
        $testFunction = $service->createImage($sequence, $window, $GC, $AT, $KETO, $GmC, $oligo_skew_array, $olen, $from, $to, $name);

        $this->assertEquals($sExpected, $testFunction);
    }
}