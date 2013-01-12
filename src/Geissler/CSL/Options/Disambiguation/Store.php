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
     * Store the values form the object.
     * @param \Geissler\CSL\Interfaces\Disambiguate $disambiguation
     */
    public function store(Disambiguate $disambiguation)
    {
        if (count($disambiguation->getDisambiguate()) > 0) {
            foreach ($disambiguation->getDisambiguate() as $id => $citation) {
                if (isset($this->ambiguous[$id]) == true) {
                    Container::getRendered()->updateCitation($id, $citation, $this->ambiguous[$id], true);
                }
            }
        }

        if (count($disambiguation->getAmbiguous()) > 0) {
            foreach ($disambiguation->getAmbiguous() as $id => $citation) {
                if (isset($this->ambiguous[$id]) == true) {
                    Container::getRendered()->updateCitation($id, $citation, $this->ambiguous[$id]);
                }
            }
        }
    }
}
