<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\Children;
use Geissler\CSL\Rendering\ExpandFormatting;
use Geissler\CSL\Container;
use Geissler\CSL\Options\Disambiguation\Disambiguation;
use Geissler\CSL\Options\CiteCollapsing;

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

        /*
        var_dump($return);
        */

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
        Container::getCitationItem()->moveToFirst();
        $result =   array();

        do {
            $group = array();
            do {
                $id =   Container::getActualId();

                if ($id !== null) {
                    Container::getData()->moveToId($id);

                    // store rendered citation
                    $tmp = $this->renderJustActualEntry($data);
                    Container::getRendered()->addCitation($id, $tmp);
                    $group[] = $id;
                }
            } while (Container::getCitationItem()->nextInGroup() == true);

            $result[]   =   $group;
        } while (Container::getCitationItem()->next() == true);


        return explode("\n", $this->addCitationOptions($this->disambiguateCites($result, "\n"), "\n"));
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
        $result =   array();

        do {
            // store rendered citation
            $id =   Container::getData()->getVariable('id');
            Container::getRendered()->addCitation($id, $this->renderJustActualEntry($data));
            $result[]   =   $id;
        } while (Container::getData()->next() == true);

        return array(
            $this->collapseCitations(
                $this->disambiguateCites($result, $this->delimiter),
                $this->delimiter
            )
        );
    }

    /**
     * Disambiguate ambiguous cites and restore the render values.
     *
     * @param $data
     * @return mixed
     */
    private function disambiguateCites($data)
    {
        // disambiguate cites
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
            || Container::getContext()->getValue('disambiguateAddGivenname', 'citation') === true
            || Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') === true
            || Container::getContext()->isChooseDisambiguationActive() == true) {
            $disambiguation =   new Disambiguation();
            $disambiguation->solve();
        }

        // move to starting position for citations and citations items
        $citationData   =   false;
        if (Container::getCitationItem() !== false) {
            $citationData   =   true;
            Container::getCitationItem()->moveToFirst();
        }
        // replace item ids by disambiguate cite
        $length =   count($data);
        for ($i = 0; $i < $length; $i++) {
            if (is_array($data[$i]) == true) {
                $innerLength    =   count($data[$i]);
                for ($j = 0; $j < $innerLength; $j++) {
                    // re-render citation if missing
                    $actualCitation =   Container::getRendered()->getCitationById($data[$i][$j]);
                    if ($actualCitation == false) {
                        Container::getContext()->enter('disambiguation');
                        $data[$i][$j]   =   $this->renderJustActualEntry('');
                        Container::getContext()->leave();
                    } else {
                        $data[$i][$j]   =   $actualCitation;
                    }

                    // Add delimiter at end if not ending with a dot
                    // (see affix_SuppressDelimiterCharsWhenFullStopInSuffix.txt)
                    if ($j < $innerLength - 1) {
                        if (preg_match('/\.$/', $data[$i][$j]) == 0) {
                            $data[$i][$j] .=  $this->delimiter;
                        } else {
                            $data[$i][$j] .= ' ';
                        }
                    }

                    // move to next in group
                    if ($citationData == true) {
                        Container::getCitationItem()->nextInGroup();
                    }
                }

                $data[$i]   =   $this->collapseCitations($data[$i], $this->delimiter);
            } else {
                // re-render citation if missing
                $actualCitation =   Container::getRendered()->getCitationById($data[$i]);
                if ($actualCitation == false) {
                    Container::getContext()->enter('disambiguation');
                    $data[$i]   =   $this->renderJustActualEntry('');
                    Container::getContext()->leave();
                } else {
                    $data[$i]   =   $actualCitation;
                }
            }

            if ($citationData == true) {
                Container::getCitationItem()->next();
            }
        }

        // remove all additional temporary disambiguation options
        Container::getContext()->clearDisambiguationOptions();
        return $data;
    }

    /**
     * Apply cite collapsing.
     *
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    private function collapseCitations($data, $delimiter)
    {
        if (Container::getContext()->getValue('collapse', 'citation') !== '') {
            $collapse   =   new CiteCollapsing();
            return $this->format($collapse->collapse($data, $delimiter));
        } else {
            return $this->addCitationOptions($data, $delimiter);
        }
    }

    /**
     * Implode the cites and remove duplicated delimiters.
     *
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    private function addCitationOptions($data, $delimiter)
    {
        // remove wrong or duplicated delimiters (see affix_SuppressDelimiterCharsWhenFullStopInSuffix.txt)
        $return =   implode($delimiter, $data);
        $return =   str_replace('. ' . $this->delimiter, '. ', $return);
        $return =   str_replace($this->delimiter . $this->delimiter, $this->delimiter, $return);

        return $this->format($return);
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
