<?php
namespace Geissler\CSL\Data;

use Geissler\CSL\Container;

/**
 * Storage for the rendered citations and bibliographies entries.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Rendered
{
    /** @var array */
    private $rendered;
    /** @var array */
    private $replace;
    /** @var bool */
    private $useDifferentCitations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rendered                 =   array();
        $this->replace                  =   array();
        $this->useDifferentCitations    =   false;
    }

    /**
     * Set if the first cite is different from the following.
     *
     * @param boolean $useDifferentCitations
     * @return Rendered
     */
    public function setUseDifferentCitations($useDifferentCitations)
    {
        $this->useDifferentCitations = $useDifferentCitations;
        return $this;
    }

    /**
     * Retrieve the configuration of the different citation usage.
     *
     * @return bool
     */
    public function getUseDifferentCitations()
    {
        return $this->useDifferentCitations;
    }

    /**
     * Store a rendered citation under its id.
     *
     * @param string $id
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addCitation($id, $value)
    {
        if ($this->useDifferentCitations == false
            || isset($this->rendered[$id]['firstCitation']) == true) {
            return $this->store($id, $value, 'citation');
        } else {
            return $this->store($id, $value, 'firstCitation');
        }
    }

    /**
     * Update a already rendered citation or the first citation by comparison of the entries.
     *
     * @param string $id
     * @param string $value
     * @param string $valueToUpdate old value, which should be replaced
     * @param bool $force
     * @return Rendered
     */
    public function updateCitation($id, $value, $valueToUpdate, $force = false)
    {
        if ($this->getUseDifferentCitations() == true
            && isset($this->rendered[$id]['firstCitation']) == true
            && $this->rendered[$id]['firstCitation'] == $valueToUpdate) {
            return $this->store($id, $value, 'firstCitation');
        } elseif ($force == true
            || isset($this->rendered[$id]['citation']) == false
            || $this->rendered[$id]['citation'] == $valueToUpdate) {
            return $this->store($id, $value, 'citation');
        }

        return $this;
    }

    /**
     * Retrieve all rendered values for the id.
     *
     * @param integer $id
     * @return array|bool
     */
    public function getById($id)
    {
        if (isset($this->rendered[$id]) == true) {
            return $this->rendered[$id];
        }

        return false;
    }

    /**
     * Retrieve the actual citation, first if first access and first citation is used.
     *
     * @param integer $id
     * @return string|bool
     */
    public function getCitationById($id)
    {
        $return =   $this->getById($id);
        if ($return !== false) {
            if (isset($return['firstCitation']) == true
                && $return['firstCitation'] !== '') {
                $this->store($id, '', 'firstCitation');
                return $return['firstCitation'];
            } elseif (isset($return['citation']) == true) {
                return $return['citation'];
            }
        }

        return false;
    }

    /**
     * Retrieve all rendered entries of a given type (citation, firstCitation etc.)
     *
     * @param string $type
     * @return array
     */
    public function getAllByType($type)
    {
        $return =   array();

        foreach ($this->rendered as $id => $entry) {
            if (isset($entry[$type]) == true) {
                $return[$id]    =   $entry[$type];
            }
        }

        return $return;
    }

    /**
     * Remove all rendered values.
     *
     * @return Rendered
     */
    public function clear()
    {
        $this->rendered =   array();
        return $this;
    }

    /**
     * Store the rendered value.
     *
     * @param integer $id
     * @param string $value
     * @param string $type
     * @return Rendered
     */
    private function store($id, $value, $type)
    {
        if (isset($this->rendered[$id]) == false) {
            $this->rendered[$id]    =   array('id' => $id);
        }

        $this->rendered[$id][$type]    =   $value;
        return $this;
    }
}
