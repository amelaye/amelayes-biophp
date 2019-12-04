<?php


namespace Tests\AppBundle\Service;

use AppBundle\Entity\IO\Collection;
use AppBundle\Entity\IO\CollectionElement;
use AppBundle\Entity\Sequencing\Authors;
use AppBundle\Entity\Sequencing\Features;
use AppBundle\Entity\Sequencing\Sequence;
use AppBundle\Service\IO\DatabaseManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParseGenbankManagerTest extends WebTestCase
{
    public function testFetch()
    {
        $collection = new Collection();
        $collection->setId(1);
        $collection->setNomCollection("humandb");

        $collectionElement = new CollectionElement();
        $collectionElement->setIdElement("NM_031438");
        $collectionElement->setFileName("human.seq");
        $collectionElement->setDbFormat("GENBANK");
        $collectionElement->setSeqCount(1);
        $collectionElement->setLineNo(0);
        $collectionElement->setCollection($collection);

        $mockedEm = $this->createMock(EntityManager::class);

        // Mock CollectionElement
        $repo = $this->createMock(EntityRepository::class);
        $mockedEm->expects($this->once())
            ->method('getRepository')
            ->with(CollectionElement::class)
            ->willReturn($repo);
        $repo->expects($this->once())->method('findOneBy')
            ->with(['idElement' => "NM_031438"])
            ->willReturn($collectionElement);

        $databaseManager = new DatabaseManager($mockedEm);
        $oParseSwisprotManager = $databaseManager->fetch("NM_031438");

        // Accession
        $aExpectedAccession = [];
        $this->assertEquals($aExpectedAccession, $oParseSwisprotManager->getAccession());

        $oExpectedSequence = new Sequence();
        $oExpectedSequence->setPrimAcc("NM_031438");
        $oExpectedSequence->setSeqLength(3488);
        $oExpectedSequence->setMolType("mRNA");
        $oExpectedSequence->setDate("29-DEC-2018");
        $oExpectedSequence->setSource("Homo sapiens (human)");
        $sSequence = "aagactgcat ccggctccag gaaaagcgag tgggatatcc caatctttgg actgcatcct ggttgcctct actgtggtca ";
        $sSequence.= "cctttgggaa gaaatgtctt ctgtaaaaag aagtctgaag caagaaatag ttactcagtt tcactgttca gctgctgaag ";
        $sSequence.= "gagatattgc caagttaaca ggaatactca gtcattctcc atctcttctc aatgaaactt ctgaaaatgg ctggactgct ";
        $sSequence.= "ttaatgtatg cggcaaggaa tgggcaccca gagatagtcc aatttctgct tgagaaaggg tgtgacagat caattgtcaa ";
        $sSequence.= "taaatcaagg cagactgcac tggacattgc tgtattttgg ggttataagc atatagctaa tttactagct actgctaaag ";
        $sSequence.= "gtgggaagaa gccttggttc ctaacgaatg aagtggaaga atgtgaaaat tattttagca aaacactact ggaccggaaa ";
        $sSequence.= "agtgaaaaga gaaataattc tgactggctg ctagctaaag aaagccatcc agccacagtt tttattcttt tctcagattt ";
        $sSequence.= "aaatcccttg gttactctag gtggcaataa agaaagtttc caacagccag aagttaggct ttgtcagctg aactacacag ";
        $sSequence.= "atataaagga ttatttggcc cagcctgaga agatcacctt gatttttctt ggagtagaac ttgaaataaa agacaaacta ";
        $sSequence.= "cttaattatg ctggtgaagt cccgagagag gaggaagatg gattggttgc ctggtttgct ctaggtatag atcctattgc ";
        $sSequence.= "tgctgaagaa ttcaagcaaa gacatgaaaa ttgttacttt cttcatcctc ctatgccagc ccttctgcaa ttgaaagaaa ";
        $sSequence.= "aagaagctgg ggttgtagct caagcaagat ctgttcttgc ctggcacagt cgatacaagt tttgcccaac ctgtggaaat ";
        $sSequence.= "gcaactaaaa ttgaagaagg tggctataag agattatgtt taaaagaaga ctgtcctagt ctcaatggcg tccataatac ";
        $sSequence.= "ctcataccca agagttgatc cagtagtaat catgcaagtt attcatccag atgggaccaa atgcctttta ggcaggcaga ";
        $sSequence.= "aaagatttcc cccaggcatg tttacttgcc ttgctggatt tattgagcct ggagagacaa tagaagatgc tgttaggaga ";
        $sSequence.= "gaagtagaag aggaaagtgg agtcaaagtt ggccatgttc agtatgttgc ttgtcaacca tggccaatgc cttcctcctt ";
        $sSequence.= "aatgattggt tgcttagctc tagcagtgtc tacagaaatt aaagttgaca agaatgaaat agaggatgcc cgctggttca ";
        $sSequence.= "ctagagaaca ggtcctggat gttctgacca aagggaagca gcaggcattc tttgtgccac caagccgagc tattgcacat ";
        $sSequence.= "caattaatca aacactggat tagaataaat cctaatctct aaatctaaga actaagcttt gagtattatt taataatttc ";
        $sSequence.= "taataacact cattcctcaa gtgatattag agattattca gtactcttga gagtgtcaca acacaaaata cgatgttggg ";
        $sSequence.= "ttttcgaaat attttcaaag tgttctgtct taatcacaaa ttcatatttt tacacatttt tacaatattg cctcagatta ";
        $sSequence.= "tgttaaattt gggtcagtct tctctgaact ttttctctct tggtttcttt tcttccttca cagttttatc tcacaaaacc ";
        $sSequence.= "atttttctaa taagagacat catgttggaa agatgttcta gaaatgtgca taaatttcag tgcctcttgt aagcattaaa ";
        $sSequence.= "ctgatgatga agaaagttcc tgatttgaga aatgaatcaa agtaatttta atgaattttt agcttgtatt agcttgagtt ";
        $sSequence.= "agctggcatt gattttttag tccttttgtt acctttaagt tgtcaatata tggtttttgt tcatctcccc attgtagtcc ";
        $sSequence.= "cacttgctct ttcctggggg ttccattgtt ctagcagtgg aggtgttata gtgtcgccac tcgtctaatt tgaccagtgt ";
        $sSequence.= "taagaatttt ctaatttaat aatttaatag tgatctcaat accacaccct catggaagga gaaaagcata ctattatatc ";
        $sSequence.= "tgggacctct cttttagacc taaaattaat taacatatct acttatatgt tacttatacc taaagctgtt attaagacaa ";
        $sSequence.= "accaagattc tctgcttttg cactgaaatt aaacttgaaa ggaattctcc tcaaaggtcg gatattaaat aagtcccagg ";
        $sSequence.= "cagatttaca tatttaattt aaaacattgg ctttatttca ttttgtgatg agtgatgtat ctgtgttaac aaaaaattgt ";
        $sSequence.= "ataatcatta ccaatactat ttattatgct caaatatatc ttggctttga ccttatttca acacattcta agaagccttg ";
        $sSequence.= "acaaagtaag tatattttag agctgaatca gtaagattct agagaaagca aaacatagta gttcacaatt ttgcaacata ";
        $sSequence.= "gaaagtcaca ttttgaaagg ctattttgaa attgatttaa tagctattat agtttatgaa tatcaaaatt tgtataattt ";
        $sSequence.= "gcatctttac taatgtatgc tagagctaca agagacctta aggataatat atgaaattag ctttccttat tttatagata ";
        $sSequence.= "aggaaaaaga aattgtgaaa ggtgaattta cctaattagt gaaagttaca taactaatta caacagtctg tactatataa ";
        $sSequence.= "tgcagaggac gattctccct gtaaaaggaa ctagaagcta ttactaaaaa tatatataga caaaattaaa agaaggaatg ";
        $sSequence.= "ataagaataa atttaattta ccaaatattg ttaattaaaa ttttagatac ttaacattta tttaacttaa ataaaagata ";
        $sSequence.= "actgtcagat aaaactttat tttactaatg agcagtgatt ttcttaggaa ttgatgaagg cttattggta tcaagaattt ";
        $sSequence.= "aaaccaaatt aaaactgaca gaggacattt agatacataa taaaattcga gctacataag tatatggaaa ataatgtacc ";
        $sSequence.= "ttgattatta tgaaatagag catcttgaaa ttcagtttta ctctaaatgt acttttaata cttgcagatt ctaagattac ";
        $sSequence.= "attgtaaaat tccaggtttt cataatgtta aaataggaaa gtagaatata aagtatcaac aagtgtagtt atacattttg ";
        $sSequence.= "ttttggatat ttaatcctta cttgggaaaa aatcagcatc taggtaaatt attattttaa taacaactct taaattgcca ";
        $sSequence.= "acctctgaga ggtgaaaagc tatgtaaata gaaggaatgg ccagttcaaa agaatagtag atgtgatagt gccgtgaatg ";
        $sSequence.= "tattctactg gaaatgaatg taataataca ttaaattttt aaaatcta";
        $oExpectedSequence->setSequence($sSequence);
        $oExpectedSequence->setDescription("Homo sapiens nudix hydrolase 12 (NUDT12), transcript variant 1, mRNA.");
        $oExpectedSequence->setOrganism("Homo sapiens");
        $this->assertEquals($oExpectedSequence, $oParseSwisprotManager->getSequence());

        $aExpectedAuthors = [];
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("1");
        $oAuthor->setAuthor("Sahni N");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("1");
        $oAuthor->setAuthor("Yi S");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("1");
        $oAuthor->setAuthor("Taipale M");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("1");
        $oAuthor->setAuthor("Fuxman Bass JI");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("1");
        $oAuthor->setAuthor("Coulombe-Huntington J");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Lopez S");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Buil A");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Souto JC");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Casademont J");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Martinez-Perez A");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Almasy L");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("2");
        $oAuthor->setAuthor("Soria JM");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Xie T");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Deng L");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Mei P");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Zhou Y");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Wang B");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Wei Y");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Zhang X");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("3");
        $oAuthor->setAuthor("Xu R");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("4");
        $oAuthor->setAuthor("Del-Aguila JL");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("4");
        $oAuthor->setAuthor("Beitelshees AL");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("4");
        $oAuthor->setAuthor("Cooper-Dehoff RM");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("5");
        $oAuthor->setAuthor("Hek K");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("5");
        $oAuthor->setAuthor("Demirkan A");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("5");
        $oAuthor->setAuthor("Teumer A");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("5");
        $oAuthor->setAuthor("Cornelis MC");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("5");
        $oAuthor->setAuthor("Amin N");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("6");
        $oAuthor->setAuthor("Siedlinski M");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("6");
        $oAuthor->setAuthor("Crapo JD");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("6");
        $oAuthor->setAuthor("Silverman EK");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("7");
        $oAuthor->setAuthor("Wang AG");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("7");
        $oAuthor->setAuthor("Yoon SY");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("7");
        $oAuthor->setAuthor("Oh JH");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("7");
        $oAuthor->setAuthor("Jeon YJ");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("7");
        $oAuthor->setAuthor("Kim M");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("8");
        $oAuthor->setAuthor("Abdelraheim SR");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("8");
        $oAuthor->setAuthor("Spiller DG");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("8");
        $oAuthor->setAuthor("McLennan AG");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("9");
        $oAuthor->setAuthor("Simpson JC");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("9");
        $oAuthor->setAuthor("Wellenreuther R");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("9");
        $oAuthor->setAuthor("Poustka A");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("9");
        $oAuthor->setAuthor("Pepperkok R");
        $aExpectedAuthors[] = $oAuthor;
        $oAuthor = new Authors();
        $oAuthor->setPrimAcc("NM_031438");
        $oAuthor->setRefno("9");
        $oAuthor->setAuthor("Wiemann S");
        $aExpectedAuthors[] = $oAuthor;
        $this->assertEquals($aExpectedAuthors, $oParseSwisprotManager->getAuthors());

        $aExpectedFeatures = [];
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("source 1..3488");
        $oFeature->setFtQual("organism");
        $oFeature->setFtValue("Homo sapiens");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("source 1..3488");
        $oFeature->setFtQual("mol_type");
        $oFeature->setFtValue("mRNA");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("source 1..3488");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("taxon:9606");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("source 1..3488");
        $oFeature->setFtQual("chromosome");
        $oFeature->setFtValue("5");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("source 1..3488");
        $oFeature->setFtQual("map");
        $oFeature->setFtValue("5q21.2");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("gene 1..3488");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("gene 1..3488");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("nudix hydrolase 12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("gene 1..3488");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("GeneID:83594");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("gene 1..3488");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("HGNC:HGNC:18826");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("gene 1..3488");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("MIM:609232");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1..87");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1..87");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 88..299");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 88..299");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("EC_number");
        $oFeature->setFtValue("3.6.1.22");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("note");
        $note = "isoform 1 is encoded by transcript variant 1; nucleoside diphosphate linked moiety X-type motif 12; ";
        $note.= "peroxisomal NADH pyrophosphatase NUDT12; nudix motif 12; nucleoside diphosphate-linked moiety X motif 12; nudix ";
        $note.= "(nucleoside diphosphate linked moiety X)-type motif 12";
        $oFeature->setFtValue($note);
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("codon_start");
        $oFeature->setFtValue("1");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("product");
        $oFeature->setFtValue("peroxisomal NADH pyrophosphatase NUDT12 isoform 1");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("protein_id");
        $oFeature->setFtValue("NP_113626.1");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("CCDS:CCDS4096.1");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("GeneID:83594");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("HGNC:HGNC:18826");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("db_xref");
        $oFeature->setFtValue("MIM:609232");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("CDS 94..1482");
        $oFeature->setFtQual("translation");
        $translation = "MSSVKRSLKQEIVTQFHCSAAEGDIAKLTGILSHSPSLLNETSE ";
        $translation.= "NGWTALMYAARNGHPEIVQFLLEKGCDRSIVNKSRQTALDIAVFWGYKHIANLLATAK ";
        $translation.= "GGKKPWFLTNEVEECENYFSKTLLDRKSEKRNNSDWLLAKESHPATVFILFSDLNPLV ";
        $translation.= "TLGGNKESFQQPEVRLCQLNYTDIKDYLAQPEKITLIFLGVELEIKDKLLNYAGEVPR ";
        $translation.= "EEEDGLVAWFALGIDPIAAEEFKQRHENCYFLHPPMPALLQLKEKEAGVVAQARSVLA ";
        $translation.= "WHSRYKFCPTCGNATKIEEGGYKRLCLKEDCPSLNGVHNTSYPRVDPVVIMQVIHPDG ";
        $translation.= "TKCLLGRQKRFPPGMFTCLAGFIEPGETIEDAVRREVEEESGVKVGHVQYVACQPWPM ";
        $translation.= "PSSLMIGCLALAVSTEIKVDKNEIEDARWFTREQVLDVLTKGKQQAFFVPPSRAIAHQ ";
        $translation.= "LIKHWIRINPNL";
        $oFeature->setFtValue($translation);
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 124..213");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 124..213");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 124..213");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("propagated from UniProtKBSwiss-Prot (Q9BQG2.1); Region: ANK 1");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 226..315");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 226..315");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 226..315");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("propagated from UniProtKBSwiss-Prot (Q9BQG2.1); Region: ANK 2");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 325..387");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 325..387");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 325..387");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("propagated from UniProtKBSwiss-Prot (Q9BQG2.1); Region: ANK 3");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 646..648");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 646..648");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 646..648");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("N6-succinyllysine. {ECO:0000250|UniProtKB:Q9DCN1}; propagated from UniProtKBSwiss-Prot (Q9BQG2.1); modified site");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 967..969");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 967..969");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 967..969");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("N6-succinyllysine. {ECO:0000250|UniProtKB:Q9DCN1}; propagated from UniProtKBSwiss-Prot (Q9BQG2.1); modified site");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1156..1221");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1156..1221");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1156..1221");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("propagated from UniProtKBSwiss-Prot (Q9BQG2.1); Region: Nudix box");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1471..1479");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1471..1479");
        $oFeature->setFtQual("experiment");
        $oFeature->setFtValue("experimental evidence, no additional details recorded");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("misc_feature 1471..1479");
        $oFeature->setFtQual("note");
        $oFeature->setFtValue("propagated from UniProtKBSwiss-Prot (Q9BQG2.1); Region: Microbody targeting signal. {ECO:0000305}");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 300..889");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 300..889");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 890..1057");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 890..1057");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1058..1171");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1058..1171");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1172..1371");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1172..1371");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1372..3488");
        $oFeature->setFtQual("gene");
        $oFeature->setFtValue("NUDT12");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Features();
        $oFeature->setPrimAcc("NM_031438");
        $oFeature->setFtKey("exon 1372..3488");
        $oFeature->setFtQual("inference");
        $oFeature->setFtValue("alignment:Splign:2.1.0");
        $aExpectedFeatures[] = $oFeature;
        $this->assertEquals($aExpectedFeatures, $oParseSwisprotManager->getFeatures());

    }
}