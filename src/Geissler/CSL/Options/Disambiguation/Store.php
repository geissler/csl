<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Container;

/**
 * Stores the ambiguous and disambiguated values from an object in the rendered container.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @licence MIT
 */
class Store
{
    /** @var array */
    private $ambiguous;

    /**
     * Set the starting ambiguous values.
     *
     * @param array $ambiguous
     * @return Store
     */
    public function setAmbiguous(array $ambiguous)
    {
        $this->ambiguous = $ambiguous;
        return $this;
    }

    /**
     * Store the values from the object.
     * @param \Geissler\CSL\Interfaces\Disambiguate $disambiguation
     */
    public function store(Disambiguate $disambiguation)
    {
        $disambiguated  =   $disambiguation->getDisambiguate();
        if ($disambiguated === null) {
            $disambiguated  =   array();
        }

        if (count($disambiguated) > 0) {
            foreach ($disambiguated as $id => $citation) {
                if (isset($this->ambiguous[$id]) == true) {
                    Container::getRendered()->update($id, $this->ambiguous[$id], $citation);
                }
            }
        }

        // use modified ambiguous values
        if (count($disambiguation->getAmbiguous()) > 0
            && $this->ambiguous !== $disambiguation->getAmbiguous()) {
            foreach ($disambiguation->getAmbiguous() as $id => $citation) {
                if (isset($this->ambiguous[$id]) == true
                    && array_key_exists($id, $disambiguated) == false) {
                    Container::getRendered()->update($id, $this->ambiguous[$id], $citation);
                }
            }
        }
    }
}
