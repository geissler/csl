<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Container;

/**
 * Is numeric test.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class IsNumeric implements Chooseable
{
    /** @var string **/
    private $variable;

    /**
     * Parses the IsNumeric configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->variable =   '';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'is-numeric') {
                $this->variable =   (string) $value;
            }
        }
    }

    /**
     * Tests if the variable is numeric.
     *
     * @return boolea
     */
    public function validate()
    {
        if (preg_match(
            '/^[0-9]+([ ]{0,1}[&|\-|,][ ]{0,1}[0-9]+)*$|^[A-z]{0,1}[0-9]+[A-z]{0,1}$|^[0-9]+[A-z]{2,3}$/',
            Container::getData()->getVariable($this->variable)
        ) == 1) {

            return true;
        }

        return false;
    }
}
