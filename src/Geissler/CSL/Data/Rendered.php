<?php
namespace Geissler\CSL\Data;

/**
 * Storage for the rendered citations until they are disambiguated.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Rendered
{
    /** @var array */
    private $rendered;
    /** @var bool */
    private $differentCitations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rendered = array();
        $this->differentCitations = false;
    }

    /**
     * De-/Activate the usage of different citations.
     *
     * @param boolean $value
     * @return Rendered
     */
    public function setUseDifferentCitations($value)
    {
        $this->differentCitations = $value;
        return $this;
    }

    /**
     * Retrieve the different citation usage.
     *
     * @return bool
     */
    public function getUseDifferentCitations()
    {
        return $this->differentCitations;
    }

    /**
     * Retrieve the number of actual rendered cites.
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->rendered);
    }

    /**
     * Store a rendered value under its item-id and citationID.
     *
     * @param string|int $id
     * @param string|int $citationId
     * @param string $value
     * @return Rendered
     */
    public function set($id, $citationId, $value)
    {
        $key = $this->find($id, $citationId);
        if ($key !== false) {
            $this->rendered[$key]['value'] = $value;
        } else {
            $this->rendered[] = array(
                'id' => $id,
                'citationId' => $citationId,
                'value' => $value
            );
        }

        return $this;
    }

    /**
     * Retrieve a rendered value based on the item-id and citationID.
     * @param string|int $id
     * @param bool|string|int  $citationId
     * @return bool|string
     */
    public function get($id, $citationId = false)
    {
        if ($citationId === false) {
            if (strpos($id, '#') === false) {
                return false;
            }

            $ids = explode('#', $id);
            $id = $ids[0];
            $citationId = $ids[1];
        }

        $key = $this->find($id, $citationId);
        if ($key !== false) {
            return $this->rendered[$key]['value'];
        }

        return false;
    }

    /**
     * Replace the rendered value for a item-id by a new value.
     *
     * @param string|int $id
     * @param string $oldValue
     * @param string $newValue
     * @return Rendered
     */
    public function update($id, $oldValue, $newValue)
    {
        $length = count($this->rendered);
        $updated = array();

        for ($i = 0; $i < $length; $i++) {
            if (isset($this->rendered[$i]) == true
                && $this->rendered[$i]['id'] == $id
                && ($this->rendered[$i]['value'] == $oldValue
                    || preg_match('/^' . $oldValue . '/', $this->rendered[$i]['value']) == 1)
                && ($this->differentCitations == false
                    || in_array($id, $updated) == false)
            ) {

                $this->rendered[$i]['value'] = $newValue;
                $updated[] = $id;
            }
        }

        return $this;
    }

    /**
     * Retrieve all rendered values based on there item-id.
     *
     * @return array Array with item-ids as keys and rendered value as value
     */
    public function getAllById()
    {
        $return = array();
        foreach ($this->rendered as $values) {
            if (array_key_exists($values['id'], $return) == false) {
                $return[$values['id']] = $values['value'];
            }
        }

        return $return;
    }

    /**
     * Retrieve the rendered value of the first cite using the given bibliography entry.
     *
     * @param string $id
     * @return string
     */
    public function getFirstById($id)
    {
        $length = count($this->rendered);

        for ($i = 0; $i < $length; $i++) {
            if (isset($this->rendered[$i]) == true
                && $this->rendered[$i]['id'] == $id
            ) {
                return $this->rendered[$i]['value'];
            }
        }

        return null;
    }

    /**
     * Retrieve the position of the first usage of a bibliography entry.
     *
     * @param string $id
     * @return int
     */
    public function getPositionOfFirstId($id)
    {
        $length = count($this->rendered);

        for ($i = 0; $i < $length; $i++) {
            if ($this->rendered[$i]['id'] == $id) {
                return $i + 1;
            }
        }

        return null;
    }

    public function dump()
    {
        var_dump($this->rendered);
    }

    /**
     * Remove all rendered values.
     *
     * @return Rendered
     */
    public function clear()
    {
        $this->rendered = array();
        return $this;
    }

    /**
     * Remove all rendered values with a given item-id.
     *
     * @param int|string $id
     * @return \Geissler\CSL\Data\Rendered
     */
    public function clearById($id)
    {
        $length = count($this->rendered);

        for ($i = 0; $i < $length; $i++) {
            if (isset($this->rendered[$i]) == true
                && $this->rendered[$i]['id'] == $id
            ) {
                unset($this->rendered[$i]);
            }
        }

        return $this;
    }

    /**
     * Find the position of a rendered value by its item-id and citationID.
     *
     * @param int|string $id
     * @param int|string $citationId
     * @return bool|int|string
     */
    private function find($id, $citationId)
    {
        foreach ($this->rendered as $key => $values) {
            if ($values['id'] == $id
                && $values['citationId'] == $citationId
            ) {
                return $key;
            }
        }

        return false;
    }
}
