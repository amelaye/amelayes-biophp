<?php
/**
 * SubMatrix Entity
 * Freely inspired by BioPHP's project biophp.org
 * Created 11 february 2019
 * Last modified 3 november 2019
 */
namespace AppBundle\Service;

/**
 * This class allows the use of customized substitution matrices. See tech doc for details.
 * @author Amélie DUVERNET aka Amelaye <amelieonline@gmail.com>
 */
class SubMatrix
{
    /**
     * Rules of the matrix
     * @var array
     */
    private $rules;

    /**
     * submatrix simply initializes the rules property to the empty array.
     */
    public function __construct()
    {
        $this->rules = [];
    }

    /**
     * Gets the rules of the matrix
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Adds a rule to the substitution matrix.
     * @param type $x
     */
    public function addrule($x)
    {
        $x = func_get_args();
        array_push($this->rules, $x);
    }
}