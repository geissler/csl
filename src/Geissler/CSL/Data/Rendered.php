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
    /** @var array **/
    private $citation;
    /** @var array */
    private $bibliography;
    /** @var array */
    private $disambiguation;

    /** @var array */
    private $rendered;
    /** @var array */
    private $replace;
    private $useDifferentCitations;

    public function __construct()
    {
        $this->citation         =   array();
        $this->bibliography     =   array();
        $this->disambiguation   =   array();

        $this->rendered                 =   array();
        $this->replace                  =   array();
        $this->useDifferentCitations    =   false;
    }

    public function setUseDifferentCitations($useDifferentCitations)
    {
        $this->useDifferentCitations = $useDifferentCitations;
        return $this;
    }

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
            || $this->rendered[$id]['citation'] == $valueToUpdate) {
            return $this->store($id, $value, 'citation');
        }

        return $this;
    }

    /**
     * Store a rendered citation under its id.
     *
     * @param string $id
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addDisambiguation($id, $value)
    {
        return $this->store($id, $value, 'disambiguation');
    }

    /**
     * Store a rendered citation under its id.
     *
     * @param string $id
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addSuffix($id, $value)
    {
        return $this->store($id, $value, 'suffix');
    }

    /**
     * Store a rendered citation under its id.
     *
     * @param string $id
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addAmbiguous($id, $value)
    {
        return $this->store($id, $value, 'ambiguous');
    }

    /**
     * Store a rendered citation under its id.
     *
     * @param string $id
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addBibliography($id, $value)
    {
        return $this->store($id, $value, 'bibliography');
    }

    public function getById($id)
    {
        if (isset($this->rendered[$id]) == true) {
            return $this->rendered[$id];
        }

        return false;
    }

    public function getCitationById($id)
    {
        $return =   $this->getById($id);
        if ($return !== false) {
            if (isset($return['firstCitation']) == true
                && $return['firstCitation'] !== '') {
                $this->store($id, '', 'firstCitation');
                return $return['firstCitation'];
            }

            return $return['citation'];
        }

        return false;
    }

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

    public function getByValue($value, $type)
    {
        foreach ($this->rendered as $entry) {
            if (isset($entry[$type]) == true
                && $entry[$type] == $value) {
                return $entry;
            }
        }

        return false;
    }

    public function getOtherByValue($value, $type, $selfId)
    {
        foreach ($this->rendered as $entry) {
            if (isset($entry[$type]) == true
                && $entry[$type] == $value
                && $entry['id'] !== $selfId) {
                return $entry;
            }
        }

        return false;
    }

    /*
        public function isAmbiguous($value, $selfId)
        {
            foreach ($this->rendered as $id => $entry) {
                if (((isset($entry['ambiguous']) == true
                        && $entry['ambiguous'] == $value)
                    || (isset($entry['disambiguation']) == true
                        && $entry['disambiguation'] == $value))
                    && $selfId != $id) {
                    return true;
                }
            }

            return false;
        }
    */

    /**
     * Finds the last used suffix for an ambiguous cite.
     *
     * @param string $value
     * @return string
     */
    public function findLastSuffix($value)
    {
        $suffix =   'a';

        foreach ($this->rendered as $entry) {
            if (isset($entry['ambiguous']) == true
                && $entry['ambiguous'] == $value
                && isset($entry['suffix']) == true
                && strcmp($entry['suffix'], $suffix) > 0) {
                $suffix =   $entry['suffix'];
            }
        }

        return $suffix;
    }

    /**
     * Add a rendered citation to the replace list to replace previously rendered citations.
     *
     * @param string $target
     * @param string $value
     * @return \Geissler\CSL\Data\Rendered
     */
    public function addReplace($target, $value)
    {
        $this->replace[] = array(
            'target'    =>  $target,
            'replace'   =>  $value
        );
        return $this;
    }

    /**
     * Replace all previously rendered citations with the one containing a suffix.
     *
     * @param string|array $value
     * @return string|array
     */
    public function replace($value)
    {
        if (is_array($value) == true) {
            $length =   count($value);

            for ($i = 0; $i < $length; $i++) {
                $value[$i]  =   $this->replaceValue($value[$i]);
            }

            return $value;
        } else {
            return $this->replaceValue($value);
        }

    }

    /**
     * @param $value
     * @return mixed
     */
    private function replaceValue($value)
    {
        foreach ($this->replace as $replace) {
            $value  =   preg_replace(
                '/([\b|;|\.|,|\]|\[|\(|\)]){0,1}' . preg_quote($replace['target']) . '([\b|;|\.|,|\]|\[|\(|\)])/',
                '$1' . $replace['replace'] . '$2',
                $value
            );
        }

        return $value;
    }

    /**
     * @param $id
     * @param $value
     * @param $type
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
