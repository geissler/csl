<?php
namespace Geissler\CSL\Interfaces;

use Geissler\CSL\Options\Disambiguation\Store;

/**
 * Disambiguate.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Disambiguate
{
    /**
     * Sets the array with the ambiguous values.
     *
     * @param array $ambiguous
     * @return Disambiguate
     */
    public function setAmbiguous(array $ambiguous);

    /**
     * Get the ambiguous values.
     *
     * @return array
     */
    public function getAmbiguous();

    /**
     * Set the array with the disambiguated values.
     *
     * @param array $disambiguate
     * @return Disambiguate
     */
    public function setDisambiguate(array $disambiguate);

    /**
     * Get the disambiguate values.
     *
     * @return array
     */
    public function getDisambiguate();

    /**
     * Set the next object to disambiguate ambiguous values.
     *
     * @param \Geissler\CSL\Interfaces\Disambiguate $successor
     * @return Disambiguate
     */
    public function setSuccessor(Disambiguate $successor);

    /**
     * Set the store object with the starting ambiguous values injected.
     *
     * @param \Geissler\CSL\Options\Disambiguation\Store $store
     * @return Disambiguate
     */
    public function setStore(Store $store);

    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate();
}
