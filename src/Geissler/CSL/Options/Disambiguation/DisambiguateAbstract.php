<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\Store;
use Geissler\CSL\Container;

/**
 * Implementation of all methods used in more than one Disambiguate class.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @licence MIT
 */
abstract class DisambiguateAbstract implements Disambiguate
{
    /** @var array */
    private $ambiguous;
    /** @var array */
    private $disambiguate;
    /** @var Disambiguate */
    private $successor;
    /** @var Store */
    private $store;

    /**
     * Sets the array with the ambiguous values.
     *
     * @param array $ambiguous
     * @return Disambiguate
     */
    public function setAmbiguous(array $ambiguous)
    {
        $this->ambiguous = $ambiguous;
        return $this;
    }

    /**
     * Get the ambiguous values.
     *
     * @return array
     */
    public function getAmbiguous()
    {
        return $this->ambiguous;
    }

    /**
     * Set the array with the disambiguated values.
     *
     * @param array $disambiguate
     * @return Disambiguate
     */
    public function setDisambiguate(array $disambiguate)
    {
        $this->disambiguate = $disambiguate;
        return $this;
    }

    /**
     * Get the disambiguate values.
     *
     * @return array
     */
    public function getDisambiguate()
    {
        return $this->disambiguate;
    }

    /**
     * Set the next object to disambiguate ambiguous values.
     *
     * @param \Geissler\CSL\Interfaces\Disambiguate $successor
     * @return Disambiguate
     */
    public function setSuccessor(Disambiguate $successor)
    {
        $this->successor = $successor;
        return $this;
    }

    /**
     * @return Disambiguate
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * Set the store object with the starting ambiguous values injected.
     *
     * @param \Geissler\CSL\Options\Disambiguation\Store $store
     * @return Disambiguate
     */
    public function setStore(Store $store)
    {
        $this->store    =   $store;
        return $this;
    }

    /**
     * @return \Geissler\CSL\Options\Disambiguation\Store
     */
    private function getStore()
    {
        return $this->store;
    }

    /**
     * Store the values with the Store object.
     *
     * @param array $disambiguated
     * @param array $ambiguous
     */
    protected function store($disambiguated, $ambiguous = array())
    {
        if (is_array($ambiguous) == true
            && count($ambiguous) > 0) {
            $this->setAmbiguous($ambiguous);
        }

        if (is_array($disambiguated) == true
            && count($disambiguated) > 0) {
            $this->setDisambiguate($disambiguated);
        }

        $this->getStore()->store($this);
    }

    /**
     * Call next successor if exists, otherwise store the values.
     *
     * @param array $disambiguated
     * @param array $ambiguous
     */
    protected function succeed($disambiguated, $ambiguous)
    {
        if (is_object($this->getSuccessor()) == false) {
            $this->store($disambiguated, $ambiguous);
            return;
        }

        if (is_array($ambiguous) == true
            && count($ambiguous) > 0) {
            $this->getSuccessor()->setAmbiguous($ambiguous);
        }

        if (is_array($disambiguated) == true
            && count($disambiguated) > 0) {
            $this->getSuccessor()->setDisambiguate($disambiguated);
        }

        $this->getSuccessor()->disambiguate();
    }

    /**
     * Renders the names for the given ids as array or string.
     *
     * @param array $data
     * @param bool $asArray
     * @return array
     */
    protected function renderNames($data, $asArray = false)
    {
        $names  =   Container::getContext()->get('layout', 'layout')->getChildElement('\Geissler\CSL\Names\Names');
        if (is_object($names) == false) {
            return array();
        }

        $ids    =   array_keys($data);
        $length =   count($ids);
        $values =   array();
        for ($i = 0; $i < $length; $i++) {
            $id =   $ids[$i];
            Container::getData()->moveToId($id);

            if ($asArray == false) {
                $values[$id]    =   $names->render('');
            } else {
                $values[$id]    =   $names->renderAsArray('');
            }
        }

        return $values;
    }
}
