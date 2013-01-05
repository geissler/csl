<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\Children;
use Geissler\CSL\Rendering\ExpandFormatting;
use Geissler\CSL\Container;
use Geissler\CSL\Options\CiteCollapsing;
use Geissler\CSL\Options\Disambiguation;

/**
 * Layout.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Layout implements Renderable, Parental
{
    /** @var Affix **/
    private $affix;
    /** @var Formatting **/
    private $formatting;
    /** @var string **/
    private $delimiter;
    /** @var ExpandFormatting **/
    private $expand;
    /** @var array **/
    private $children;

    /**
     * Parses the layout configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->delimiter    =   "";

        $this->affix        =   new Affix($xml);
        $this->formatting   =   new Formatting($xml);
        $this->expand       =   new ExpandFormatting();

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'delimiter') {
                $this->delimiter    =   (string) $value;
            }
        }

        $children       =   new Children();
        $this->children =   $children->create($xml);
    }

    /**
     * Retrieve the first child element matching the given class name.
     *
     * @param string $class full, namespace aware class name
     * @return object
     */
    public function getChildElement($class)
    {
        foreach ($this->children as $child) {
            if (($child instanceof $class) == true) {
                return $child;
            } elseif (($child instanceof \Geissler\CSL\Interfaces\Parental) == true) {
                $subChild   =   $child->getChildElement($class);

                if ($subChild !== false) {
                    return $subChild;
                }
            }
        }

        return false;
    }

    /**
     * Tests if the element or an child element is accessing the variable with the given name.
     *
     * @param string $name
     * @return boolean
     */
    public function isAccessingVariable($name)
    {
        foreach ($this->children as $child) {
            if (($child instanceof \Geissler\CSL\Interfaces\Parental) == true
                && $child->isAccessingVariable($name) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Renders all child element with the data of all Data entries.
     *
     * @param mixed $data
     * @return string|array
     */
    public function render($data)
    {
        Container::getContext()->enter('layout', array('layout' => $this));

        if (Container::getContext()->getName() == 'citation') {
            if (Container::getCitationItem() !== false) {
                $return =   $this->citation($data);
            } else {
                $return =   $this->citationFromData($data);
            }
        } else {
            $return =   $this->bibliography($data);
        }

        Container::getContext()->leave();
        return $return;
    }

    public function renderJustActualEntry($data)
    {
        $entry   =   array();
        if (Container::getCitationItem() !== false) {
            // prefix for citation item
            if (Container::getCitationItem()->get('prefix') !== null) {
                $entry[]    =   Container::getCitationItem()->get('prefix');
                $entry[]    =   ' ';
            }

            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }

            // suffix for citation item
            if (Container::getCitationItem()->get('suffix') !== null) {
                $entry[]    =   ' ';
                $entry[]    =   Container::getCitationItem()->get('suffix');
            }
        } else {
            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }
        }

        return implode('', $entry);
    }

    public function renderById($id, $data)
    {
        $actualId   =   Container::getActualId();
        $return     =   false;

        if (Container::getData()->moveToId($id) == true) {
            $return =   $this->renderJustActualEntry($data);
            Container::getData()->moveToId($actualId);
        }

        return $return;
    }

    private function citation($data)
    {
        Container::getCitationItem()->moveToFirst();
        $result =   array();

        do {
            $group = array();
            do {
                $id =   Container::getActualId();
                Container::getData()->moveToId($id);

                // store rendered citation
                Container::getRendered()->addCitation($id, $this->renderJustActualEntry($data));
                $group[] = $id;
            } while (Container::getCitationItem()->nextInGroup() == true);

            $result[]   =   $group;
        } while (Container::getCitationItem()->next() == true);

        return $this->addCitationOptions($result);
    }

    /**
     * Render citations with the bibliography input data.
     *
     * @param $data
     * @return array
     */
    private function citationFromData($data)
    {
        Container::getData()->moveToFirst();
        $result         =   array();
        $disambiguation =   new Disambiguation();

        do {
            // disambiguate and store rendered citation
            $id         =   Container::getData()->getVariable('id');
            $entity     =   $disambiguation->solve($this->renderJustActualEntry($data), $id);
            $result[]   =   $entity;
            Container::getRendered()->addCitation($id, $entity);
        } while (Container::getData()->next() == true);

        return array($this->format($this->addCitationOptions($result, $this->delimiter)));
    }

    private function addCitationOptions($data)
    {
        // disambiguate cites
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
            || Container::getContext()->getValue('disambiguateAddGivenname', 'citation') === true
            || Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') === true) {
            $disambiguation =   new Disambiguation();
            $disambiguation->solve();
        }

        // replace item ids by disambiguate cite
        $length =   count($data);
        for ($i = 0; $i < $length; $i++) {
            if (is_array($data[$i]) == true) {
                $innerLength    =   count($data[$i]);
                for ($j = 0; $j < $innerLength; $j++) {
                    $data[$i][$j]   =   Container::getRendered()->getCitationById($data[$i][$j]);

                    // Add delimiter at end if not ending with a dot
                    // (see affix_SuppressDelimiterCharsWhenFullStopInSuffix.txt)
                    if (preg_match('/\.$/', $data[$i][$j]) == 0) {
                        $data[$i][$j] .=  $this->delimiter;
                    } else {
                        $data[$i][$j] .= ' ';
                    }
                }
            } else {
                $data[$i]   =   Container::getRendered()->getCitationById($data[$i]);
            }
        }

        // $this->format($this->addCitationOptions($group, $this->delimiter));
        // Disambiguate cites


        // Collapsing options
        if (Container::getContext()->getValue('collapse', 'citation') !== '') {
            $collapse   =   new CiteCollapsing();
            return $collapse->collapse($data, $this->delimiter);
        }

        // remove wrong or duplicated delimiters (see affix_SuppressDelimiterCharsWhenFullStopInSuffix.txt)
        $return =   implode($this->delimiter, $data);
        $return =   str_replace('. ' . $this->delimiter, '. ', $return);
        return str_replace($this->delimiter . $this->delimiter, $this->delimiter, $return);
    }

    private function bibliography($data)
    {
        Container::getData()->moveToFirst();
        $result =   array();

        do {
            $entry   =   array();
            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }
            $result[]   =   $this->format(implode('', $entry));
        } while (Container::getData()->next() == true);

        return $result;
    }

    /**
     * Apply additional formatting and remove duplicated spaces and dots.
     *
     * @param $data
     * @return string
     */
    private function format($data)
    {
        $data   =   preg_replace('/[ ][ ]+/', ' ', $data);
        $data   =   preg_replace('/[\.][\.]+/', ' ', $data);
        $data   =   preg_replace('/( ,)/', ',', $data);
        $data   =   preg_replace('/\.(<\/[a-z]+>)\./', '.$1', $data);
        $data   =   $this->formatting->render($data);
        $data   =   $this->expand->render($data);
        return $this->affix->render($data);
    }
}
