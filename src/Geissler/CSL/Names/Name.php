<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Interfaces\Contextualize;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Names\NamePart;

/**
 * Name element as child of names.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Name implements Renderable, Modifiable, Contextualize
{
    /** @var string **/
    private $and;
    /** @var string **/
    private $delimiter;
    /** @var string **/
    private $delimiterPrecedesEtAl;
    /** @var string **/
    private $delimiterPrecedesLast;
    /** @var integer **/
    private $etAlMin;
    /** @var string **/
    private $etAlUseFirst;
    /** @var integer **/
    private $etAlSubsequentMin;
    /** @var string **/
    private $etAlSubsequentUseFirst;
    /** @var boolean **/
    private $etAlUseLast;
    /** @var string **/
    private $form;
    /** @var boolean **/
    private $initialize;
    /** @var string **/
    private $initializeWith;
    /** @var string **/
    private $nameAsSortOrder;
    /** @var string **/
    private $sortSeparator;
    /** @var Affix **/
    private $affix;
    /** @var Formatting **/
    private $formatting;
    /** @var array **/
    private $nameParts;
    /** @var array */
    private $literals;
    private $backup;

    /**
     * Parses the Name configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->and                      =   '';
        $this->delimiter                =   ', ';
        $this->delimiterPrecedesEtAl    =   'contextual';
        $this->delimiterPrecedesLast    =   'contextual';
        $this->etAlMin                  =   0;
        $this->etAlUseFirst             =   '';
        $this->etAlSubsequentMin        =   0;
        $this->etAlSubsequentUseFirst   =   '';
        $this->etAlUseLast              =   false;

        $this->form             =   'long';
        $this->initialize       =   true;
        $this->initializeWith   =   false;
        $this->nameAsSortOrder  =   '';
        $this->sortSeparator    =   ', ';
        $this->nameParts        =   array();
        $this->literals         =   array();

        $this->affix        =   new Affix($xml);
        $this->formatting   =   new Formatting($xml);

        $this->modify($xml);

        foreach ($xml->children() as $child) {
            if ($child->getName() == 'name-part') {
                $this->nameParts[]  =   new NamePart($child);
            }
        }

        $this->backup();
    }

    /**
     * Modifies the configuration of the name by parsing a new \SimpleXMLElement.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Interfaces\Modifiable|\Geissler\CSL\Names\Name
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'and':
                    $this->and  =   (string) $value;
                    break;
                case 'delimiter':
                    $this->delimiter  =   (string) $value;
                    break;
                case 'delimiter-precedes-et-al':
                    $this->delimiterPrecedesEtAl  =   (string) $value;
                    break;
                case 'delimiter-precedes-last':
                    $this->delimiterPrecedesLast  =   (string) $value;
                    break;
                case 'et-al-min':
                    $this->etAlMin  =   (int) $value;
                    break;
                case 'et-al-use-first':
                    $this->etAlUseFirst  =   (int) $value;
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin  =   (int) $value;
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst  =   (string) $value;
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast  =   ((string) $value == 'true' ? true : false);
                    break;
                case 'form':
                    $this->form  =   (string) $value;
                    break;
                case 'initialize':
                    $this->initialize  =   ((string) $value == 'true' ? true : false);
                    break;
                case 'initialize-with':
                    $this->initializeWith  =   (string) $value;
                    break;
                case 'name-as-sort-order':
                    $this->nameAsSortOrder  =   (string) $value;
                    break;
                case 'sort-separator':
                    $this->sortSeparator  =   (string) $value;
                    break;
            }
        }

        $this->backup();

        return $this;
    }

    /**
     * Retrieve all configuration options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            'and' => $this->and,
            'delimiter' => $this->delimiter,
            'delimiter-precedes-et-al' => $this->delimiterPrecedesEtAl,
            'delimiter-precedes-last' => $this->delimiterPrecedesLast,
            'et-al-min' => $this->etAlMin,
            'et-al-use-first' => $this->etAlUseFirst,
            'et-al-subsequent-min' => $this->etAlSubsequentMin,
            'et-al-subsequent-use-first' => $this->etAlSubsequentUseFirst,
            'et-al-use-last' => $this->etAlUseLast,
            'form' => $this->form,
            'initialize' => $this->initialize,
            'initialize-with' => $this->initializeWith,
            'name-as-sort-order' => $this->nameAsSortOrder,
            'sort-separator' => $this->sortSeparator
        );
    }

    /**
     * Applies the context configuration and the disambiguation options to the object.
     *
     * @return \Geissler\CSL\Interfaces\Contextualize
     */
    public function apply()
    {
        // restore configuration parsed from csl file
        foreach ($this->backup as $property => $value) {
            if ($value !== null) {
                $this->$property    =   $value;
            }
        }

        foreach (Container::getContext()->getOptions() as $name => $value) {
            if (property_exists($this, $name) == true) {
                $this->$name    =   $value;
            }
        }

        if (Container::getContext()->getDisambiguationOptions('Geissler\CSL\Names\Name') !== false) {
            $options    =   Container::getContext()->getDisambiguationOptions('Geissler\CSL\Names\Name');
            foreach ($options as $name => $value) {
                if (property_exists($this, $name) == true) {
                    $this->$name    =   $value;
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve a configuration parameter from the originally parsed name element.
     *
     * @param string $property
     * @return string|boolean|null
     */
    public function getPropertyValue($property)
    {
        if (isset($this->backup[$property]) == true) {
            return $this->backup[$property];
        }

        return null;
    }

    /**
     * Renders the names.
     *
     * @param array $data
     * @return string
     */
    public function render($data)
    {
        $this->apply();
        $names  =   array();
        $length =   count($data);

        if ($length == 0) {
            return '';
        }

        for ($i = 0; $i < $length; $i++) {
            $names[]    =   $this->formatName($data[$i], $i);
        }

        $etAl           =   false;
        $etAlUseFirst   =   $this->etAlUseFirst;
        $etAlMin        =   $this->etAlMin;

        // If used, the values of these attributes replace those of respectively et-al-min and et-al-use-first
        // for subsequent cites (cites referencing earlier cited items)
        if (Container::getRendered()->getById(Container::getActualId()) !== false) {
            if ($this->etAlSubsequentMin > 0) {
                $etAlMin    =   $this->etAlSubsequentMin;
            }

            if ($this->etAlSubsequentUseFirst !== '') {
                $etAlUseFirst   =   $this->etAlSubsequentUseFirst;
            }
        }

        if ($etAlMin > 0
            && $length > 1
            && $etAlMin <= $length
            && $etAlUseFirst < $length) {
            $etAl   =   true;
        }

        $namesAndSplitter   =   array();
        $and                =   $this->getAndDelimiter();
        for ($i = 0; $i < $length; $i++) {
            $namesAndSplitter[] =   $names[$i];

            if ($etAl == true
                && $i == $etAlUseFirst - 1) {

                switch ($this->delimiterPrecedesEtAl) {
                    case 'contextual':
                        if ($etAlUseFirst >= 2) {
                            $namesAndSplitter[] =   $this->delimiter;
                        }
                        break;
                    case 'after-inverted-name':
                        if ($this->nameAsSortOrder == 'first') {
                            $namesAndSplitter[] =   $this->delimiter;
                        }
                        break;
                    case 'always':
                        $namesAndSplitter[] =   $this->delimiter;
                        break;
                }

                // et-al Term
                $namesAndSplitter[] =   ' ';
                $namesAndSplitter[] =   Container::getLocale()->getTerms('et-al');
                break;
            }

            // The delimiter between the second to last and last name of the names in a name variable
            if ($i == $length - 2
                && $and !== ''
                && in_array($names[$i], $this->literals) == false) {
                switch ($this->delimiterPrecedesLast) {
                    case 'contextual':
                        if ($length >= 3) {
                            $namesAndSplitter[] =   $this->delimiter;
                        }
                        break;
                    case 'after-inverted-name':
                        if ($this->nameAsSortOrder == 'first') {
                            $namesAndSplitter[] =   $this->delimiter;
                        }
                        break;
                    case 'always':
                        $namesAndSplitter[] =   $this->delimiter;
                        break;
                    case 'never':
                        break;
                }

                if ($this->and !== '') {
                    $namesAndSplitter[] =   ' ';
                    $namesAndSplitter[] =   $and;
                    $namesAndSplitter[] =   ' ';
                }
            } elseif ($i < $length - 1) {
                $namesAndSplitter[] =   $this->delimiter;
            }

        }
        $return =   str_replace('  ', ' ', implode('', $namesAndSplitter));

        // do not connect literals with an and
        if (count($this->literals) > 0) {
            $and    =   $and . ' ';
            foreach ($this->literals as $literal) {
                $return =   str_replace($and . $literal, $literal, $return);
            }
        }

        $return =   $this->formatting->render($return);
        return $this->affix->render($return);
    }

    /**
     * Formats the name at a position.
     *
     * @param array $data
     * @param integer $position
     * @return string
     */
    private function formatName($data, $position)
    {
        // get name parts in the specific order
        $sort   =   $this->getDisplayAndSortOrder($data, $position);
        $names  =   array();
        foreach ($sort['display'] as $name) {
            if (isset($data[$name]) == true) {
                $names[$name]    =   $data[$name];
            }
        }

        // initialize given names
        if (isset($names['given']) == true) {
            if ($this->initialize == true
                && $this->initializeWith !== false) {

                $names['given']  =  preg_replace(
                    '/([A-Z])[a-z]+\b[ ]{0,1}/',
                    '$1' . $this->initializeWith,
                    $names['given']
                );
                $names['given'] =   preg_replace('/([a-z]+)/', ' $1 ', $names['given']);
                $names['given'] =   trim(preg_replace('/[ ]+/', ' ', $names['given']));

            } else {
                $names['given']  =  trim(preg_replace('/([A-Z]\b)/', '$1' . $this->initializeWith, $names['given']));
            }

            // Hyphenation of Initialized Names
            if (Container::getContext()->get('initializeWithHyphen') == false) {
                $names['given'] =   str_replace($this->initializeWith . '-', $this->initializeWith, $names['given']);
            }

            if (Container::getContext()->getDisambiguationOption('Geissler\CSL\Names\Name', 'trimGivenName') == true) {
                $names['given'] =   str_replace(' ', '', $names['given']);
            }
        }

        // format name-parts
        if (count($this->nameParts) > 0) {
            foreach ($this->nameParts as $namePart) {
                $names[$namePart->getName()] =   $namePart->render($names);
            }

            $names['suffix']  =   '';
            $names['non-dropping-particle']  =   '';
            $names['dropping-particle']  =   '';
        } elseif (isset($data['literal']) == true) {
            // institutions
            $literal            =   $this->stripEnglishArticles($data['literal']);
            $this->literals[]   =   $literal;
            $names[]            =   $literal;
        }

        $return = implode(' ', $names);

        if ($sort['switched'] == true
            && isset($names['family']) == true
            && isset($names['given']) == true) {
            $return = str_replace(
                $names['family'] . ' ' . $names['given'],
                $names['family'] . $this->sortSeparator . $names['given'],
                $return
            );
        }

        return trim(preg_replace('/[ ]+/', ' ', $return));
    }

    /**
     * Defines the order of the names.
     *
     * @param array $data
     * @param integer $position
     * @return array Array containing the order for displaying (display) and sorting (sort) name elements.
     */
    private function getDisplayAndSortOrder($data, $position)
    {
        $return =   array(
            'display'   =>  array(),
            'sort'      =>  array(),
            'switched'  =>  false);

        if (isset($data['family']) == false) {
            return $return;
        }

        if (preg_match('/\p{Cyrillic}+|\p{Latin}+/u', $data['family']) == 1) {
            // Display order of latin/Cyrillic names
            $demoteNonDroppingParticle  =   Container::getContext()->getValue('demoteNonDroppingParticle');

            if ($this->form ==  'long') {
                if (($this->nameAsSortOrder == 'first'
                        && $position == 0)
                    || $this->nameAsSortOrder == 'all') {

                    $return['switched'] =   true;

                    if ($demoteNonDroppingParticle == 'never'
                        || $demoteNonDroppingParticle == 'sort-only') {
                            $return['display'][]    =   'non-dropping-particle';
                            $return['display'][]    =   'family';
                            $return['display'][]    =   'given';
                            $return['display'][]    =   'dropping-particle';
                            $return['display'][]    =   'suffix';
                    } else {
                        $return['display'][]    =   'family';
                        $return['display'][]    =   'given';
                        $return['display'][]    =   'dropping-particle';
                        $return['display'][]    =   'non-dropping-particle';
                        $return['display'][]    =   'suffix';
                    }
                } else {
                    $return['display'][]    =   'given';
                    $return['display'][]    =   'dropping-particle';
                    $return['display'][]    =   'non-dropping-particle';
                    $return['display'][]    =   'family';
                    $return['display'][]    =   'suffix';
                }

            } else {
                $return['display'][]    =   'non-dropping-particles';
                $return['display'][]    =   'family';
            }

            // Sorting order of latin/Cyrillic names
            if ($demoteNonDroppingParticle == 'never') {
                $return['sort'][]    =   'non-dropping-particle';
                $return['sort'][]    =   'family';
                $return['sort'][]    =   'dropping-particle';
                $return['sort'][]    =   'given';
                $return['sort'][]    =   'suffix';
            } else {
                $return['sort'][]    =   'family';
                $return['sort'][]    =   'dropping-particle';
                $return['sort'][]    =   'non-dropping-particle';
                $return['sort'][]    =   'given';
                $return['sort'][]    =   'suffix';
            }
        } else {
            // Display and sorting order of non-latin/Cyrillic names
            if ($this->form == 'long') {
                $return['display'][]    =   'family';
                $return['display'][]    =   'given';
                $return['sort'][]       =   'family';
                $return['sort'][]       =   'given';
            } else {
                $return['display'][]    =   'family';
                $return['sort'][]       =   'family';
            }
        }

        return $return;
    }

    private function stripEnglishArticles($nonPersonal)
    {
        if (strpos(Container::getLocale()->getLanguage(), 'en-') !== false) {
            $nonPersonal    =   preg_replace('/^(an|a|the) /i', '', $nonPersonal);
        }

        return $nonPersonal;
    }

    /**
     * Retrieve the and term delimiter.
     *
     * @return string
     */
    private function getAndDelimiter()
    {
        if ($this->and == 'text') {
            return Container::getLocale()->getTerms('and');
        } elseif (Container::getLocale()->getTerms('symbol') !== null) {
            return Container::getLocale()->getTerms('symbol');
        } elseif ($this->and == 'symbol') {
            return '&#38;';
        }

        return '';
    }

    private function backup()
    {
        $backup         =   get_object_vars($this);
        $this->backup   =   $backup;
    }
}
