<?php
/**
 * Doctrine Entity Authors
 * Freely inspired by BioPHP's project biophp.org
 * Created 23 march 2019
 * Last modified 13 november 2019
 */
namespace AppBundle\Entity\Sequencing;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Authors
 * @package AppBundle\Entity\Sequencing
 * @author Amélie DUVERNET aka Amelaye <amelieonline@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name = "author")
 */
class Author
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity = "AppBundle\Entity\Sequencing\Reference",
     *     inversedBy="primAcc"
     * )
     * @ORM\JoinColumn(
     *     name = "prim_acc",
     *     referencedColumnName = "prim_acc"
     * )
     */
    private $primAcc;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(
     *     type = "integer",
     *     length = 11,
     *     nullable = false,
     *     options = {"default":0}
     * )
     */
    private $refno;

    /**
     * @var string
     * @ORM\Column(
     *     type = "string",
     *     length = 50,
     *     nullable = false
     * )
     */
    private $author;

    /**
     * @return string
     */
    public function getPrimAcc()
    {
        return $this->primAcc;
    }

    /**
     * @param string $primAcc
     */
    public function setPrimAcc($primAcc)
    {
        $this->primAcc = $primAcc;
    }

    /**
     * @return int
     */
    public function getRefno()
    {
        return $this->refno;
    }

    /**
     * @param int $refno
     */
    public function setRefno($refno)
    {
        $this->refno = $refno;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
}