<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;

/**
 * Abtract class which implents the matching validation for Chooseable's.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
abstract class ChooseableAbstract implements Chooseable
{
    /** @var array **/
    private $variable;
    /** @var string **/
    private $match;

    /**
     * Sets the variable and matching option.
     *
     * @param string $variable
     * @param string $match
     */
    public function __construct($variable, $match = 'all')
    {
        $this->variable =   explode(' ', (string) $variable);
        $this->match    =   $match;
    }

    /**
     * Tests if all variables validate the matching criteria.
     *
     * @return boolean
     */
    public function validate()
    {
        switch ($this->match) {
            case 'all':
                foreach ($this->variable as $variable) {
                    if ($this->validateVariable($variable) == false) {
                        return false;
                    }
                }
                return true;
                break;
            case 'any':
                foreach ($this->variable as $variable) {
                    if ($this->validateVariable($variable) == true) {
                        return true;
                    }
                }
                return false;
                break;
            case 'none':
                foreach ($this->variable as $variable) {
                    if ($this->validateVariable($variable) == true) {
                        return false;
                    }
                }
                return true;
                break;
        }

        return false;
    }

    /**
     * Tests the variable.
     *
     * @param string $variable
     * @return boolean
     */
    abstract protected function validateVariable($variable);
}
