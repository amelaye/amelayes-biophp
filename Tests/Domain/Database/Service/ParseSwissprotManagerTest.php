<?php
namespace Tests\Domain\Database\Service;

use Amelaye\BioPHP\Domain\Database\Entity\Collection;
use Amelaye\BioPHP\Domain\Database\Entity\CollectionElement;
use Amelaye\BioPHP\Domain\Sequence\Entity\Author;
use Amelaye\BioPHP\Domain\Sequence\Entity\Feature;
use Amelaye\BioPHP\Domain\Sequence\Entity\GbSequence;
use Amelaye\BioPHP\Domain\Sequence\Entity\Keyword;
use Amelaye\BioPHP\Domain\Sequence\Entity\Reference;
use Amelaye\BioPHP\Domain\Sequence\Entity\Sequence;
use Amelaye\BioPHP\Domain\Sequence\Entity\SpDatabank;
use Amelaye\BioPHP\Domain\Sequence\Entity\SrcForm;
use Amelaye\BioPHP\Domain\Database\Service\DatabaseManager;
use Amelaye\BioPHP\Domain\Database\Service\ParseSwissprotManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ParseSwissprotManagerTest extends TestCase
{
    public function testFetch()
    {
        $collection = new Collection();
        $collection->setId(1);
        $collection->setNomCollection("humandbSwiss");

        $collectionElement = new CollectionElement();
        $collectionElement->setIdElement("1375");
        $collectionElement->setFileName("basicswiss.txt");
        $collectionElement->setDbFormat("SWISSPROT");
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
            ->with(['idElement' => "1375"])
            ->willReturn($collectionElement);

        $databaseManager = new DatabaseManager($mockedEm, './data/');
        $oParseSwisprotManager = $databaseManager->fetch("1375");

        // Accession
        $aExpectedAccession = [];
        $this->assertEquals($aExpectedAccession, $oParseSwisprotManager->getAccession());

        // Sequences
        $oExpectedSequence = new Sequence();
        $oExpectedSequence->setPrimAcc("P01375");
        $oExpectedSequence->setEntryName("FA");
        $oExpectedSequence->setSeqLength(233);
        $oExpectedSequence->setMolType("PRT;");
        $oExpectedSequence->setDate("21-JUL-1986");
        $oExpectedSequence->setSource("HOMO SAPIENS (HUMAN)");
        $sSequence = "MSTESMIRDVELAEEALPKKTGGPQGSRRCLFLSLFSFLIVAGATTLFCLLHFGVIGPQREEFPRDLSLISPLAQAVRSSSRTPSDKPVAHVVAN";
        $sSequence.= "PQAEGQLQWLNRRANALLANGVELRDNQLVVPSEGLYLIYSQVLFKGQGCPSTHVLLTHTISRIAVSYQTKVNLLSAIKSPCQRETPEGAEAKPW";
        $sSequence.= "YEPIYLGGVFQLEKGDRLSAEINRPDYLDFAESGQVYFGIIAL";
        $oExpectedSequence->setSequence($sSequence);
        $oExpectedSequence->setDescription("TUMOR NECROSIS FACTOR PRECURSOR (TNF-ALPHA) (CACHECTIN).");
        $oExpectedSequence->setFragment(0);
        $aOrganism = [
          0 => "EUKARYOTA",
          1 => "METAZOA",
          2 => "CHORDATA",
          3 => "VERTEBRATA",
          4 => "TETRAPODA",
          5 => "MAMMALIA",
          6 => "EUTHERIA",
          7 => "PRIMATES",
        ];
        $oExpectedSequence->setOrganism($aOrganism);
        $this->assertEquals($oExpectedSequence, $oParseSwisprotManager->getSequence());

        // Authors
        $aExpectedAuthors = [];
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(0);
        $author->setAuthor("CHUMAKOV A.M.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(0);
        $author->setAuthor("SHINGAROVA L.N.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(0);
        $author->setAuthor("OVCHINNIKOV Y.A.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(1);
        $author->setAuthor("PALLADINO M.A.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(1);
        $author->setAuthor("KOHR W.J.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(1);
        $author->setAuthor("AGGARWAL B.B.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(1);
        $author->setAuthor("GOEDDEL D.V.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(2);
        $author->setAuthor("SHIRAI T.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(2);
        $author->setAuthor("YAMAGUCHI H.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(2);
        $author->setAuthor("ITO H.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(2);
        $author->setAuthor("TODD C.W.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(2);
        $author->setAuthor("WALLACE R.B.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(3);
        $author->setAuthor("JARRETT-NEDWIN J.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(3);
        $author->setAuthor("PENNICA D.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(3);
        $author->setAuthor("GOEDDEL D.V.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(3);
        $author->setAuthor("GRAY P.W.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(4);
        $author->setAuthor("VAN ARSDELL J.N.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(4);
        $author->setAuthor("YAMAMOTO R.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(4);
        $author->setAuthor("MARK D.F.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(5);
        $author->setAuthor("ECK M.J.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(5);
        $author->setAuthor("SPRANG S.R.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(6);
        $author->setAuthor("JONES E.Y.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(6);
        $author->setAuthor("STUART D.I.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(6);
        $author->setAuthor("WALKER N.P.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(7);
        $author->setAuthor("ECK M.J.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(7);
        $author->setAuthor("SPRANG S.R.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(8);
        $author->setAuthor("OSTADE X.V.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(8);
        $author->setAuthor("TAVERNIER J.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(8);
        $author->setAuthor("PRANGE T.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(8);
        $author->setAuthor("FIERS W.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(9);
        $author->setAuthor("STEVENSON F.T.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(9);
        $author->setAuthor("BURSTEN S.L.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(9);
        $author->setAuthor("LOCKSLEY R.M.");
        $aExpectedAuthors[] = $author;
        $author = new Author();
        $author->setPrimAcc("P01375");
        $author->setRefno(9);
        $author->setAuthor("LOVETT D.H.");
        $aExpectedAuthors[] = $author;
        $this->assertEquals($aExpectedAuthors, $oParseSwisprotManager->getAuthors());

        $aExpectedFeatures = [];
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("PROPEP");
        $oFeature->setFtFrom(1);
        $oFeature->setFtTo(76);
        $oFeature->setFtValue("PROPEP");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("CHAIN");
        $oFeature->setFtFrom(77);
        $oFeature->setFtTo(233);
        $oFeature->setFtValue("CHAIN");
        $oFeature->setFtDesc("TUMOR NECROSIS FACTOR");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TRANSMEM");
        $oFeature->setFtFrom(36);
        $oFeature->setFtTo(56);
        $oFeature->setFtValue("TRANSMEM");
        $oFeature->setFtDesc("SIGNAL-ANCHOR (TYPE-II PROTEIN)");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("LIPID");
        $oFeature->setFtFrom(19);
        $oFeature->setFtTo(19);
        $oFeature->setFtValue("LIPID");
        $oFeature->setFtDesc("MYRISTATE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("LIPID");
        $oFeature->setFtFrom(20);
        $oFeature->setFtTo(20);
        $oFeature->setFtValue("LIPID");
        $oFeature->setFtDesc("MYRISTATE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("DISULFID");
        $oFeature->setFtFrom(145);
        $oFeature->setFtTo(177);
        $oFeature->setFtValue("DISULFID");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("MUTAGEN");
        $oFeature->setFtFrom(108);
        $oFeature->setFtTo(108);
        $oFeature->setFtValue("MUTAGEN");
        $oFeature->setFtDesc("R->W: BIOLOGICALLY INACTIVE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("MUTAGEN");
        $oFeature->setFtFrom(112);
        $oFeature->setFtTo(112);
        $oFeature->setFtValue("MUTAGEN");
        $oFeature->setFtDesc("L->F: BIOLOGICALLY INACTIVE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("MUTAGEN");
        $oFeature->setFtFrom(162);
        $oFeature->setFtTo(162);
        $oFeature->setFtValue("MUTAGEN");
        $oFeature->setFtDesc("S->F: BIOLOGICALLY INACTIVE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("MUTAGEN");
        $oFeature->setFtFrom(167);
        $oFeature->setFtTo(167);
        $oFeature->setFtValue("MUTAGEN");
        $oFeature->setFtDesc("V->A,D: BIOLOGICALLY INACTIVE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("MUTAGEN");
        $oFeature->setFtFrom(222);
        $oFeature->setFtTo(222);
        $oFeature->setFtValue("MUTAGEN");
        $oFeature->setFtDesc("E->K: BIOLOGICALLY INACTIVE");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("CONFLICT");
        $oFeature->setFtFrom(63);
        $oFeature->setFtTo(63);
        $oFeature->setFtValue("CONFLICT");
        $oFeature->setFtDesc("F -> S (IN REF. 5)");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(89);
        $oFeature->setFtTo(93);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TURN");
        $oFeature->setFtFrom(99);
        $oFeature->setFtTo(100);
        $oFeature->setFtValue("TURN");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TURN");
        $oFeature->setFtFrom(109);
        $oFeature->setFtTo(110);
        $oFeature->setFtValue("TURN");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(112);
        $oFeature->setFtTo(113);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TURN");
        $oFeature->setFtFrom(115);
        $oFeature->setFtTo(116);
        $oFeature->setFtValue("TURN");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(118);
        $oFeature->setFtTo(119);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(124);
        $oFeature->setFtTo(125);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(130);
        $oFeature->setFtTo(143);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(152);
        $oFeature->setFtTo(159);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(166);
        $oFeature->setFtTo(170);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(173);
        $oFeature->setFtTo(174);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TURN");
        $oFeature->setFtFrom(183);
        $oFeature->setFtTo(184);
        $oFeature->setFtValue("TURN");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(189);
        $oFeature->setFtTo(202);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("TURN");
        $oFeature->setFtFrom(204);
        $oFeature->setFtTo(205);
        $oFeature->setFtValue("TURN");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(207);
        $oFeature->setFtTo(212);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("HELIX");
        $oFeature->setFtFrom(215);
        $oFeature->setFtTo(217);
        $oFeature->setFtValue("HELIX");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(218);
        $oFeature->setFtTo(218);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $oFeature = new Feature();
        $oFeature->setPrimAcc("P01375");
        $oFeature->setFtKey("STRAND");
        $oFeature->setFtFrom(227);
        $oFeature->setFtTo(232);
        $oFeature->setFtValue("STRAND");
        $aExpectedFeatures[] = $oFeature;
        $this->assertEquals($aExpectedFeatures, $oParseSwisprotManager->getFeatures());

        $aExpectedKeywords = [];
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("CYTOKINE");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("CYTOTOXIN");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("TRANSMEMBRANE");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("GLYCOPROTEIN");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("SIGNAL-ANCHOR");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("MYRISTYLATION");
        $aExpectedKeywords[] = $oKeyword;
        $oKeyword = new Keyword();
        $oKeyword->setPrimAcc("P01375");
        $oKeyword->setKeywords("3D-STRUCTURE");
        $aExpectedKeywords[] = $oKeyword;
        $this->assertEquals($aExpectedKeywords, $oParseSwisprotManager->getKeywords());

        $aExpectedReferences = [];
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(0);
        $oReference->setTitle("COLD SPRING HARB. SYMP. QUANT. BIOL. 51:611-624(1986).");
        $oReference->setMedline("87217060");
        $oReference->setRemark("SEQUENCE FROM N.A.");
        $oReference->setJournal("COLD SPRING HARB. SYMP. QUANT. BIOL. 51:611-624(1986).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(1);
        $oReference->setTitle("NATURE 312:724-729(1984).");
        $oReference->setMedline("85086244");
        $oReference->setRemark("SEQUENCE FROM N.A.");
        $oReference->setJournal("NATURE 312:724-729(1984).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(2);
        $oReference->setTitle("NATURE 313:803-806(1985).");
        $oReference->setMedline("85137898");
        $oReference->setRemark("SEQUENCE FROM N.A.");
        $oReference->setJournal("NATURE 313:803-806(1985).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(3);
        $oReference->setTitle("NUCLEIC ACIDS RES. 13:6361-6373(1985).");
        $oReference->setMedline("86016093");
        $oReference->setRemark("SEQUENCE FROM N.A.");
        $oReference->setJournal("NUCLEIC ACIDS RES. 13:6361-6373(1985).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(4);
        $oReference->setTitle("SCIENCE 228:149-154(1985).");
        $oReference->setMedline("85142190");
        $oReference->setRemark("SEQUENCE FROM N.A.");
        $oReference->setJournal("SCIENCE 228:149-154(1985).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(5);
        $oReference->setTitle("J. BIOL. CHEM. 264:17595-17605(1989).");
        $oReference->setMedline("90008932");
        $oReference->setRemark("X-RAY CRYSTALLOGRAPHY (2.6 ANGSTROMS).");
        $oReference->setJournal("J. BIOL. CHEM. 264:17595-17605(1989).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(6);
        $oReference->setTitle("J. CELL SCI. SUPPL. 13:11-18(1990).");
        $oReference->setMedline("91193276");
        $oReference->setRemark("X-RAY CRYSTALLOGRAPHY (2.9 ANGSTROMS).");
        $oReference->setJournal("J. CELL SCI. SUPPL. 13:11-18(1990).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(7);
        $oReference->setTitle("J. BIOL. CHEM. 264:17595-17605(1989).");
        $oReference->setMedline("90008932");
        $oReference->setRemark("X-RAY CRYSTALLOGRAPHY (2.6 ANGSTROMS).");
        $oReference->setJournal("J. BIOL. CHEM. 264:17595-17605(1989).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(8);
        $oReference->setTitle("EMBO J. 10:827-836(1991).");
        $oReference->setMedline("91184128");
        $oReference->setRemark("MUTAGENESIS.");
        $oReference->setJournal("EMBO J. 10:827-836(1991).");
        $aExpectedReferences[] = $oReference;
        $oReference = new Reference();
        $oReference->setPrimAcc("P01375");
        $oReference->setRefno(9);
        $oReference->setTitle("J. EXP. MED. 176:1053-1062(1992).");
        $oReference->setMedline("93018820");
        $oReference->setRemark("MYRISTOYLATION.");
        $oReference->setJournal("J. EXP. MED. 176:1053-1062(1992).");
        $aExpectedReferences[] = $oReference;
        $this->assertEquals($aExpectedReferences, $oParseSwisprotManager->getReferences());

        $aExpectedSpDataBank = [];
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("EMBL");
        $oSpdatabank->setPid1("X02910");
        $oSpdatabank->setPid2("HSTNFA");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("EMBL");
        $oSpdatabank->setPid1("M16441");
        $oSpdatabank->setPid2("HSTNFAB");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("EMBL");
        $oSpdatabank->setPid1("X01394");
        $oSpdatabank->setPid2("HSTNFR");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("EMBL");
        $oSpdatabank->setPid1("M10988");
        $oSpdatabank->setPid2("HSTNFAA");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("PIR");
        $oSpdatabank->setPid1("B23784");
        $oSpdatabank->setPid2("QWHUN");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("PIR");
        $oSpdatabank->setPid1("A44189");
        $oSpdatabank->setPid2("A44189");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("PDB");
        $oSpdatabank->setPid1("1TNF");
        $oSpdatabank->setPid2("15-JAN-91");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("PDB");
        $oSpdatabank->setPid1("2TUN");
        $oSpdatabank->setPid2("31-JAN-94");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("MIM");
        $oSpdatabank->setPid1("191160");
        $oSpdatabank->setPid2("11TH EDITION");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $oSpdatabank = new SpDatabank();
        $oSpdatabank->setPrimAcc("P01375");
        $oSpdatabank->setDbName("PROSITE");
        $oSpdatabank->setPid1("PS00251");
        $oSpdatabank->setPid2("TNF");
        $aExpectedSpDataBank[] = $oSpdatabank;
        $this->assertEquals($aExpectedSpDataBank, $oParseSwisprotManager->getSpDatabank());

        $oExpected = new ParseSwissprotManager();
        $oExpected->setAccession($aExpectedAccession);
        $oExpected->setSequence($oExpectedSequence);
        $oExpected->setAuthors($aExpectedAuthors);
        $oExpected->setFeatures($aExpectedFeatures);
        $oExpected->setKeywords($aExpectedKeywords);
        $oExpected->setReferences($aExpectedReferences);
        $oExpected->setSrcForm(new SrcForm());
        $oExpected->setGbSequence(new GbSequence());
        $oExpected->setSpDatabank($aExpectedSpDataBank);

        //$this->assertEquals($oExpected, $oParseSwisprotManager);
    }
}