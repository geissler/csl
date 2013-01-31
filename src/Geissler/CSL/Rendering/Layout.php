<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\Children;
use Geissler\CSL\Rendering\ExpandFormatting;
use Geissler\CSL\Container;
use Geissler\CSL\Interfaces\Option;

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
    /** @var \Geissler\CSL\Interfaces\Option */
    private $options;

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
     * Inject the additional options.
     *
     * @param \Geissler\CSL\Interfaces\Option $options
     * @return \Geissler\CSL\Rendering\Layout
     */
    public function setOptions(Option $options)
    {
        $this->options = $options;
        return $this;
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
     * Modify the first child element.
     *
     * @param string $class full, namespace aware class name
     * @param \SimpleXMLElement $xml
     * @return boolean
     */
    public function modifyChildElement($class, \SimpleXMLElement $xml)
    {
        foreach ($this->children as $child) {
            if (($child instanceof $class) == true
                && ($child instanceof \Geissler\CSL\Interfaces\Modifiable) == true) {
                $child->modify($xml);
                return true;
            } elseif (($child instanceof \Geissler\CSL\Interfaces\Parental) == true) {
                if ($child->modifyChildElement($class, $xml) == true) {
                    return true;
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
        Container::getData()->moveToFirst();

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

    /**
     * Render the actual entry.
     *
     * @param $data
     * @return string
     */
    public function renderJustActualEntry($data)
    {
        Container::getData()->moveToId(Container::getActualId());
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

    /**
     * Render entry by its id.
     *
     * @param integer|string $id
     * @param mixed $data
     * @return bool|string
     */
    public function renderById($id, $data)
    {
        $actualId   =   Container::getActualId();
        $return     =   false;

        if (Container::getData()->moveToId($id) == true) {
            $return =   $this->renderJustActualEntry($data);

            if ($actualId !== '') {
                Container::getData()->moveToId($actualId);
            }
        }

        return $return;
    }

    /**
     * Render citations with citations or citation-items data.
     *
     * @param string|array $data
     * @return array
     */
    private function citation($data)
    {
        Container::getContext()->addOption('layout', 'delimiter', $this->delimiter);
        Container::getCitationItem()->moveToFirst();
        $result =   array();

        do {
            $group = array();
            do {
                $id =   Container::getActualId();

                if ($id !== null) {
                    Container::getData()->moveToId($id);

                    // store rendered citation
                    Container::getRendered()->addCitation($id, $this->renderJustActualEntry($data));
                    $group[] = $id;
                }
            } while (Container::getCitationItem()->nextInGroup() == true);

            $result[]   =   $group;
        } while (Container::getCitationItem()->next() == true);


        return explode("\n", $this->applyCitationOptions($result, "\n"));
    }

    /**
     * Render citations with the bibliography input data.
     *
     * @param $data
     * @return array
     */
    private function citationFromData($data)
    {
        Container::getContext()->addOption('layout', 'delimiter', $this->delimiter);
        Container::getData()->moveToFirst();
        $result =   array();

        do {
            // store rendered citation
            $id =   Container::getData()->getVariable('id');
            Container::getRendered()->addCitation($id, $this->renderJustActualEntry($data));
            $result[]   =   $id;
        } while (Container::getData()->next() == true);

        return array($this->applyCitationOptions($result, $this->delimiter));
    }

    /**
     * Apply additional options on citations.
     *
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    private function applyCitationOptions($data, $delimiter)
    {
        $data   =   $this->applyOptions($data);

        if (is_array($data) == true) {
            $length     =   count($data);
            $delimiters =   array();
            for ($i = 0; $i < $length; $i++) {
                if (isset($data[$i][0]) == true) {
                    $innerLength    =   count($data[$i]);
                    $innerData      =   array();

                    for ($j = 0; $j < $innerLength; $j++) {
                        if ($data[$i][$j]['delimiter'] != '') {
                            $delimiters[]   =   $data[$i][$j]['delimiter'];
                        }

                        $innerData[]    =   $data[$i][$j]['value'] . $data[$i][$j]['delimiter'];
                    }

                    $data[$i]   =   $this->format(implode('', $innerData));
                } else {
                    if ($data[$i]['delimiter'] != '') {
                        $delimiters[]   =   $data[$i]['delimiter'];
                    }

                    $data[$i]       =   $data[$i]['value'] . $data[$i]['delimiter'];
                }
            }

            // Add delimiter where no other exists
            if (count($delimiters) > 0) {
                $regExp =   '/(' . implode('|', array_unique($delimiters)) . ')$/';
                for ($i = 0; $i < $length - 1; $i++) {
                    if (preg_match($regExp, $data[$i]) == 0) {
                        $data[$i]   .=  $delimiter;
                    }
                }

                $return =   implode('', $data);
            } else {
                $return =   implode($delimiter, $data);
            }

            $return =   str_replace('. ' . $this->delimiter, '. ', $return);
            $return =   str_replace($this->delimiter . $this->delimiter, $this->delimiter, $return);
            return $this->format($return);
        }

        return $this->format($data);
    }

    /**
     * Render the entries for the bibliography.
     *
     * @param $data
     * @return array
     */
    private function bibliography($data)
    {
        Container::getData()->moveToFirst();
        $result =   array();

        // add year-suffix to the first year rendered through cs:date in the bibliographic entry
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true
            && $this->isAccessingVariable('year-suffix') == false) {
            $this->modifyChildElement(
                'Geissler\CSL\Date\Date',
                new \SimpleXMLElement('<date add-year-suffix="true" />')
            );
        }

        do {
            $entry   =   array();
            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }

            $result[]   =   $this->format($this->applyOptions($entry, true));
        } while (Container::getData()->next() == true);

        return $this->applyOptions($result);
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
        $data   =   $this->expand->render($data);
        $data   =   $this->affix->render($data);
        return $this->formatting->render($data);
    }

    /**
     * Apply the additional options.
     *
     * @param array $data
     * @param bool $whitespaceOnly
     * @return array
     */
    private function applyOptions($data, $whitespaceOnly = false)
    {
        if (isset($this->options) == true) {
            return $this->options->apply($data, $whitespaceOnly);
        }

        return $data;
    }
}
