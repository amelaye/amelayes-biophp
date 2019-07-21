<?php
/**
 * Minitools controller
 * Freely inspired by BioPHP's project biophp.org
 * Created 23 february 2019
 * Last modified 21 july 2019
 * RIP Pasha, gone 27 february 2019 =^._.^= ∫
 */
namespace MinitoolsBundle\Controller;


use AppBundle\Bioapi\Bioapi;
use AppBundle\Service\OligosManager;

use AppBundle\Traits\OligoTrait;
use MinitoolsBundle\Entity\SequenceAlignment;
use MinitoolsBundle\Form\OligoNucleotideFrequencyType;
use MinitoolsBundle\Form\PcrAmplificationType;
use MinitoolsBundle\Form\RandomSequencesType;
use MinitoolsBundle\Form\RestrictionEnzymeDigestType;
use MinitoolsBundle\Form\SequenceAlignmentType;
use MinitoolsBundle\Service\PcrAmplificationManager;
use MinitoolsBundle\Service\RandomSequencesManager;
use MinitoolsBundle\Service\SequenceAlignmentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use MinitoolsBundle\Form\DistanceAmongSequencesType;
use MinitoolsBundle\Form\FastaUploaderType;
use MinitoolsBundle\Form\FindPalindromesType;
use MinitoolsBundle\Form\MeltingTemperatureType;
use MinitoolsBundle\Form\MicroArrayDataAnalysisType;
use MinitoolsBundle\Form\MicrosatelliteRepeatsFinderType;
use MinitoolsBundle\Service\DistanceAmongSequencesManager;
use MinitoolsBundle\Service\FastaUploaderManager;
use MinitoolsBundle\Service\FindPalindromeManager;
use MinitoolsBundle\Service\MeltingTemperatureManager;
use MinitoolsBundle\Service\MicroarrayAnalysisAdaptiveManager;
use MinitoolsBundle\Service\MicrosatelliteRepeatsFinderManager;
use MinitoolsBundle\Service\RestrictionDigestManager;

/**
 * Class MinitoolsController
 * @package MinitoolsBundle\Controller
 * @author Amélie DUVERNET akka Amelaye <amelieonline@gmail.com>
 * @todo : adding more listeners
 */
class MinitoolsController extends Controller
{
    use OligoTrait;

    private $dnaComplements;

    /**
     * MinitoolsController constructor.
     * @param Bioapi $bioapi
     */
    public function __construct(Bioapi $bioapi)
    {
        $this->dnaComplements = $bioapi->getDNAComplement();
    }

    /**
     * @Route("/minitools/distance-among-sequences", name="distance_among_sequences")
     * @param   Request                         $request
     * @param   DistanceAmongSequencesManager   $oDistanceAmongSequencesManager
     * @return  Response
     * @throws \Exception
     */
    public function distanceAmongSequencesAction(
        Request $request,
        DistanceAmongSequencesManager $oDistanceAmongSequencesManager
    )
    {
        $form = $this->get('form.factory')->create(DistanceAmongSequencesType::class);

        $oligo_array = $data = [];
        $textcluster = $seq_name = $dendogramFile = "";
        $length = 0;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $length = $formData["len"];
            $seqs = $oDistanceAmongSequencesManager->formatSequences($formData["seq"]);

            // at this moment two arrays are available: $seqs (with sequences) and $seq_names (with name of sequences)
            if ($formData["method"] == "euclidean") { // EUCLIDEAN DISTANCE
                $oligo_array = $oDistanceAmongSequencesManager->computeOligonucleotidsFrequenciesEuclidean($seqs,
                    $length, $this->dnaComplements);
                $data = $oDistanceAmongSequencesManager->computeDistancesAmongFrequenciesEuclidean($seqs,
                    $oligo_array,$length);
            } else {
                $oligo_array = $oDistanceAmongSequencesManager->computeOligonucleotidsFrequencies($seqs,
                    $this->dnaComplements);
                $data = $oDistanceAmongSequencesManager->computeDistancesAmongFrequencies($seqs, $oligo_array);
            }

            $dendogramFile = $this->getParameter('nucleotids_graphs')['dendogram_file'];
            $oDistanceAmongSequencesManager->upgmaClustering($data, $formData["method"], $length, $dendogramFile);
        }
        
        return $this->render(
            '@Minitools/Minitools/distanceAmongSequences.html.twig',
            [
                'form'              => $form->createView(),
                'oligo_array'       => $oligo_array,
                'data'              => $data,
                'length'            => $length,
                'seq_names'         => $seq_name,
                'textcluster'       => $textcluster,
                'dendogram_file'    => $dendogramFile
            ]
        );
    }

    /**
     * @Route("/minitools/find-palindromes", name="find_palindromes")
     * @param       Request                 $request
     * @param       FindPalindromeManager   $oFindPalindromeManager
     * @return      Response
     * @throws      \Exception
     */
    public function findPalindromesAction(Request $request, FindPalindromeManager $oFindPalindromeManager)
    {
        $aPalindromes = [];
        $form = $this->get('form.factory')->create(FindPalindromesType::class);
        $min = 0;
        $max = 0;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $min = $formData["min"];
            $max = $formData["max"];
            $aPalindromes = $oFindPalindromeManager->findPalindromicSeqs($formData["seq"], $min, $max);
        }

        return $this->render(
            '@Minitools/Minitools/findPalindromes.html.twig',
            [
                'form'          => $form->createView(),
                'min'           => $min,
                'max'           => $max,
                'palindromes'   => $aPalindromes
            ]
        );
    }

    /**
     * @Route("/minitools/fasta-uploader", name="gc_content_finder")
     * @param   Request                 $request
     * @param   FastaUploaderManager    $oFastaUploaderManager
     * @return  Response
     * @throws  \Exception
     */
    public function fastaUploaderAction(Request $request, FastaUploaderManager $oFastaUploaderManager)
    {
        $length = 0;
        $a = 0; $g = 0; $t = 0; $c = 0;

        $form = $this->get('form.factory')->create(FastaUploaderType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();

            $var = $oFastaUploaderManager->createFiles(
                $formData["fasta"],
                $this->getParameter('brochures_directory')
            );

            $oFastaUploaderManager->checkNucleotidSequence($var,$a,$g,$t,$c, strlen($var));
        }

        return $this->render(
            '@Minitools/Minitools/gcContentFinder.html.twig',
            [
                'form'      => $form->createView(),
                'length'    => $length,
                'a'         => $a,
                't'         => $t,
                'g'         => $g,
                'c'         => $c,
                'at'        => ($length != 0) ? (($a + $t) / $length) * 100 : null,
                'gc'        => ($length != 0) ? (($g + $c) / $length) * 100 : null
            ]
        );
    }


    /**
     * @Route("/minitools/melting-temperature", name="melting_temperature")
     * @param   Request                     $request
     * @param   MeltingTemperatureManager   $oMeltingTemperatureManager
     * @return  Response
     * @throws  \Exception
     */
    public function meltingTemperatureAction(
        Request $request,
        MeltingTemperatureManager $oMeltingTemperatureManager
    )
    {
        $iPrimer = $cg = $upper_mwt = $lower_mwt = $countATGC = $tmMin = $tmMax = 0;
        $aTmBaseStacking = [];
        $bBasic = $bNearestNeighbor = false;

        $form = $this->get('form.factory')->create(MeltingTemperatureType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $iPrimer = $formData["primer"];
            $bNearestNeighbor = $formData["nearestNeighbor"];
            $bBasic = $formData["basic"];

            $cg = $oMeltingTemperatureManager->calculateCG($iPrimer);
            $oMeltingTemperatureManager->calculateMWT($upper_mwt, $lower_mwt, $iPrimer);
            $oMeltingTemperatureManager->basicCalculations($bBasic, $iPrimer, $countATGC, $tmMin, $tmMax);
            $oMeltingTemperatureManager->neighborCalculations(
                $bNearestNeighbor,$aTmBaseStacking, $iPrimer, $formData["cp"], $formData["cs"], $formData["cmg"]
            );
        }

        return $this->render(
            '@Minitools/Minitools/meltingTemperature.html.twig',
            [
                'form'              => $form->createView(),
                'primer'            => $iPrimer,
                'basic'             => $bBasic,
                'nearest_neighbor'  => $bNearestNeighbor,
                'upper_mwt'         => $upper_mwt,
                'lower_mwt'         => $lower_mwt,
                'countATGC'         => $countATGC,
                'tm_min'            => $tmMin,
                'tm_max'            => $tmMax,
                'cg'                => $cg,
                'tmBaseStacking'    => $aTmBaseStacking
            ]
        );
    }

    /**
     * @Route("/minitools/micro-array-analysis-adaptive-quantification",
     *     name="micro_array_analysis_adaptive_quantification")
     * @param       Request                             $request
     * @param       MicroarrayAnalysisAdaptiveManager   $oMicroarrayAnalysisAdaptiveManager
     * @return      Response
     * @throws      \Exception
     */
    public function microArrayAnalysisAdaptiveQuantificationAction(
        Request $request,
        MicroarrayAnalysisAdaptiveManager $oMicroarrayAnalysisAdaptiveManager
    )
    {
        $results = array();

        $form = $this->get('form.factory')->create(MicroArrayDataAnalysisType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $results = $oMicroarrayAnalysisAdaptiveManager->processMicroarrayDataAdaptiveQuantificationMethod(
                $formData["data"]
            );
        }

        return $this->render(
            '@Minitools/Minitools/microArrayAnalysisAdaptiveQuantification.html.twig',
            [
                'form'              => $form->createView(),
                'results'           => $results
            ]
        );
    }

    /**
     * @Route("/minitools/microsatellite-repeats-finder", name="microsatellite_repeats_finder")
     * @param       Request                             $request
     * @param       MicrosatelliteRepeatsFinderManager  $oMicrosatelliteRepeatsFinderManager
     * @return      Response
     * @throws      \Exception
     */
    public function microsatelliteRepeatsFinderAction (
        Request $request,
        MicrosatelliteRepeatsFinderManager $oMicrosatelliteRepeatsFinderManager
    ) {
        $results = [];

        $form = $this->get('form.factory')->create(MicrosatelliteRepeatsFinderType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();

            $results = $oMicrosatelliteRepeatsFinderManager->findMicrosatelliteRepeats(
                $formData["sequence"],
                $formData["min"],
                $formData["max"],
                $formData["min_repeats"],
                $formData["length_of_MR"],
                $formData["mismatch"]
            );
        }

        return $this->render(
            '@Minitools/Minitools/microsatelliteRepeatsFinder.html.twig',
            [
                'form'              => $form->createView(),
                'results'           => $results
            ]
        );
    }

    /**
     * @Route("/minitools/oligonucleotide-frequency", name="oligonucleotide_frequency")
     * @param   Request         $request
     * @param   OligosManager   $oligosManager
     * @return  Response
     * @throws  \Exception
     */
    public function oligonucleotideFrequencyAction(Request $request, OligosManager $oligosManager)
    {
        $aResults = [];
        $iLength = 0;

        $form = $this->get('form.factory')->create(OligoNucleotideFrequencyType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $iLength = $formData["len"];
            $sSequence = $formData["sequence"];

            // when frequencies at both strands are requested,
            // place sequence and reverse complement of sequence in one line
            if ($formData["strands"] == 2) {
                $this->createInversion($sSequence, $this->dnaComplements);
            }

            $aResults = $oligosManager->findOligos($sSequence, $iLength, $this->dnaComplements);
            ksort($aResults);
        }

        return $this->render(
            '@Minitools/Minitools/oligonucleotideFrequency.html.twig',
            [
                'form'              => $form->createView(),
                'results'           => $aResults,
                'length'            => $iLength
            ]
        );
    }

    /**
     * @Route("/minitools/pcr-amplification", name="pcr_amplification")
     * @param       Request                     $request
     * @param       PcrAmplificationManager     $pcrAmplificationManager
     * @return      Response
     * @throws      \Exception
     */
    public function pcrAmplificationAction(Request $request, PcrAmplificationManager $pcrAmplificationManager)
    {
        $aResults = [];
        $primer1 = $primer2 = null;
        $sSequence = "";

        $form = $this->get('form.factory')->create(PcrAmplificationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $primer1 = $formData["primer1"];
            $primer2 = $formData["primer2"];
            $sSequence = $formData["sequence"];

            $sStartPattern = $pcrAmplificationManager->createStartPattern(
                $primer1,
                $primer2,
                $formData["allowmismatch"]
            );

            $sEndPattern = $pcrAmplificationManager->createEndPattern($sStartPattern);

            $aResults = $pcrAmplificationManager->amplify(
                $sStartPattern,
                $sEndPattern,
                $sSequence,
                $formData["length"]
            );
        }

        return $this->render(
            '@Minitools/Minitools/pcrAmplification.html.twig',
            [
                'form'         => $form->createView(),
                'results'      => $aResults,
                'sequence'     => $sSequence,
                'primer1'      => $primer1,
                'primer2'      => $primer2
            ]
        );
    }

    /**
     * @Route("/minitools/random-seqs", name="random_seqs")
     * @param       Request                     $request
     * @param       RandomSequencesManager      $randomSequencesManager
     * @return      Response
     * @throws      \Exception
     */
    public function randomSeqsAction(Request $request, RandomSequencesManager $randomSequencesManager)
    {
        $result             = "";
        $aAminoAcids        = [];

        $form = $this->get('form.factory')->create(RandomSequencesType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData       = $form->getData();
            switch($formData["procedure"]) {
                case "fromseq":
                    $result = $randomSequencesManager->createFromSeq($formData["length1"], $formData["seq"]);
                    break;
                case "fromACGT":
                    $length2 = $formData["length2"];
                    $aAminoAcids["A"] = $formData["dnaA"];
                    $aAminoAcids["C"] = $formData["dnaC"];
                    $aAminoAcids["G"] = $formData["dnaG"];
                    $aAminoAcids["T"] = $formData["dnaT"];
                    $result = $randomSequencesManager->createFromACGT($aAminoAcids, $length2);
                    break;
                case "fromAA":
                    $length3 = $formData["length3"];
                    $aAminoAcids["A"] = $formData["a"];
                    $aAminoAcids["C"] = $formData["c"];
                    $aAminoAcids["D"] = $formData["d"];
                    $aAminoAcids["E"] = $formData["e"];
                    $aAminoAcids["F"] = $formData["f"];
                    $aAminoAcids["G"] = $formData["g"];
                    $aAminoAcids["H"] = $formData["h"];
                    $aAminoAcids["I"] = $formData["i"];
                    $aAminoAcids["K"] = $formData["k"];
                    $aAminoAcids["L"] = $formData["l"];
                    $aAminoAcids["M"] = $formData["m"];
                    $aAminoAcids["N"] = $formData["n"];
                    $aAminoAcids["P"] = $formData["p"];
                    $aAminoAcids["Q"] = $formData["q"];
                    $aAminoAcids["R"] = $formData["r"];
                    $aAminoAcids["S"] = $formData["s"];
                    $aAminoAcids["T"] = $formData["t"];
                    $aAminoAcids["V"] = $formData["v"];
                    $aAminoAcids["W"] = $formData["w"];
                    $aAminoAcids["Y"] = $formData["y"];
                    $result = $randomSequencesManager->createFromAA($aAminoAcids, $length3);
            }
        }

        return $this->render(
            '@Minitools/Minitools/randomSeqs.html.twig',
            [
                'form'         => $form->createView(),
                'results'      => $result
            ]
        );
    }

    /**
     * @Route("/minitools/restriction-digest", name="restriction_digest")
     * @param       Request                     $request
     * @param       RestrictionDigestManager    $restrictionDigestManager
     * @param       Bioapi                      $bioapi
     * @return      Response
     * @throws      \Exception
     */
    public function restrictionDigestAction(
        Request $request,
        RestrictionDigestManager $restrictionDigestManager,
        Bioapi $bioapi
    ) {
        $sequence  = "";
        $digestion = $enzymes_array = $enzymes_array = $digestionMulti = $digestionMulti = [];
        $bShowCode = false;

        $form = $this->get('form.factory')->create(RestrictionEnzymeDigestType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $formData       = $form->getData();

            $bShowCode = $formData["showcode"];
            $sequence = $restrictionDigestManager->extractSequences($formData["sequence"]);

            // We will get info for endonucleases. The info is included within 3 different
            // functions in the bottom (for Type II, IIb and IIs enzymes)
            // Type II endonucleases are always used
            $enzymes_array = $bioapi->getTypeIIEndonucleases();

            // if TypeIIs endonucleases are requested, get them
            if (($formData["IIs"] && !$formData["defined"])) {
                $enzymes_array = array_merge($enzymes_array, $bioapi->getTypeIIsEndonucleases());
                asort($enzymes_array);
            }
            // if TypeIIb endonucleases are requested, get them
            if (($formData["IIb"] && !$formData["defined"])) {
                $enzymes_array = array_merge($enzymes_array, $bioapi->getTypeIIbEndonucleases());
                asort($enzymes_array);
            }

            $enzymes_array = $restrictionDigestManager->reduceEnzymesArray(
                $enzymes_array,
                $formData["minimum"],
                $formData["retype"],
                $formData["defined"],
                $formData["wre"]
            );

            // RESTRICTION DIGEST OF SEQUENCE
            foreach($sequence as $number => $val) {
                $digestion[$number] = $restrictionDigestManager->restrictionDigest(
                    $enzymes_array,
                    $sequence[$number]["seq"]
                );
            }

            if (sizeof($sequence) > 1) {
                $digestionMulti = $restrictionDigestManager->enzymesForMultiSeq(
                    $sequence,
                    $digestion,
                    $enzymes_array,
                    $formData["onlydiff"],
                    $formData["wre"]
                );
            }
        }

        return $this->render(
            '@Minitools/Minitools/restrictionDigest.html.twig',
            [
                'form'              => $form->createView(),
                'show_code'         => $bShowCode,
                'sequence'          => $sequence,
                'digestion'         => $digestion,
                'digestion_multi'   => array_unique($digestionMulti),
                'enzymes_array'     => $enzymes_array
            ]
        );
    }

    /**
     * @Route("/minitools/show-vendors/{enzyme}", name="show_vendors")
     * @param   string                      $enzyme
     * @param   RestrictionDigestManager    $restrictionDigestManager
     * @return  JsonResponse
     * @throws  \Exception
     */
    public function showVendorsAction($enzyme, RestrictionDigestManager $restrictionDigestManager, Bioapi $bioapi)
    {
        $message = "";
        $enzyme_array = [];
        // Get array of companies selling each endonuclease
        $vendors = $bioapi->getVendors();

        $endonuclease = preg_split("/,/", $enzyme);
        if (strpos($enzyme,",") > 0) {
            $message = "All endonucleases bellow are isoschizomers";
        }

        // print vendor for each endonuclease (uses a function)
        foreach ($endonuclease as $enzyme) {
            $enzyme_array[$enzyme] = $restrictionDigestManager->showVendors($vendors[$enzyme], $enzyme);
        }

        return new JsonResponse(["message" => $message, "vendors" => $enzyme_array]);
    }

    /**
     * @Route("/minitools/seq-alignment", name="seq_alignment")
     * @param   Request $request
     * @param   SequenceAlignmentManager $sequenceAlignmentManager
     * @return  Response
     * @throws \Exception
     */
    public function seqAlignmentAction(Request $request, SequenceAlignmentManager $sequenceAlignmentManager)
    {
        $sequenceAlignment = new SequenceAlignment();
        $form = $this->get('form.factory')->create(SequenceAlignmentType::class, $sequenceAlignment);
        $sCompare = "";
        $sAlignSeqA = "";
        $sAlignSeqB = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            /**
             * Limit sequence length to limit memory usage
             * This script creates a big array that requires a huge amount of memory
             * Do not use sequences longer than 700 bases each (1400 for both sequences)
             * In this demo, the limit has been set up to 300 bases.
             */
            $iLimit = 300;
            if ((strlen($sequenceAlignment->getSequence()) + strlen($sequenceAlignment->getSequence2())) > $iLimit) {
                throw new \Exception ("The maximum length of code accepted for both 
                sequences is $iLimit nucleotides");
            }

            // CHECK WHETHER THEY ARE DNA OR PROTEIN, AND ALIGN SEQUENCES
            if ((substr_count($sequenceAlignment->getSequence(),"A")
                    + substr_count($sequenceAlignment->getSequence(),"C")
                    + substr_count($sequenceAlignment->getSequence(),"G")
                    + substr_count($sequenceAlignment->getSequence(),"T")
                ) > (strlen($sequenceAlignment->getSequence()) / 2)) {
                // if A+C+G+T is at least half of the sequence, it is a DNA
                $aAlignment = $sequenceAlignmentManager->alignDNA(
                    $sequenceAlignment->getSequence(),
                    $sequenceAlignment->getSequence2()
                );
            } else {
                // else is protein
                $aAlignment = $sequenceAlignmentManager->alignProteins(
                    $sequenceAlignment->getSequence(),
                    $sequenceAlignment->getSequence2()
                );
            }

            // EXTRACT DATA FROM ALIGNMENT
            $sAlignSeqA = $aAlignment["seqa"];
            $sAlignSeqB = $aAlignment["seqb"];

            // COMPARE ALIGNMENTS
            $sCompare = $sequenceAlignmentManager->compareAlignment($sAlignSeqA, $sAlignSeqB);
        }

        return $this->render(
            '@Minitools/Minitools/seqAlignment.html.twig',
            [
                'form'          => $form->createView(),
                'compare'       => $sCompare,
                'align_seqa'    => $sAlignSeqA,
                'align_seqb'    => $sAlignSeqB,
                'sequence1'     => $sequenceAlignment->getId1(),
                'sequence2'     => $sequenceAlignment->getId2(),
            ]
        );
    }

    /**
     * @Route("/minitools/sequences-manipulation-and-data", name="sequences_manipulation_and_data")
     */
    public function sequencesManipulationAndDataAction()
    {
        return $this->render('@Minitools/Minitools/sequencesManipulationAndData.html.twig');
    }

    /**
     * @Route("/minitools/skews", name="skews")
     */
    public function skewsAction()
    {
        return $this->render('@Minitools/Minitools/skews.html.twig');
    }

    /**
     * @Route("/minitools/useful-formulas", name="useful_formulas")
     */
    public function usefulFormulasAction()
    {
        return $this->render('@Minitools/Minitools/usefulFormulas.html.twig');
    }
}