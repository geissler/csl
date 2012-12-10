<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Contextualize;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Names\NamePart;

/**
 * Name element as child of names.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Name implements Renderable, Contextualize
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
    /** @var Formating **/
    private $formating;
    /** @var array **/
    private $nameParts;

    /**
     * Parses the Name configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->and                      =   '';
        $this->delimiter                =   ',';
        $this->delimiterPrecedesEtAl    =   'contextual';
        $this->delimiterPrecedesLast    =   'contextual';
        $this->etAlMin                  =   0;
        $this->etAlUseFirst             =   '';
        $this->etAlSubsequentMin        =   0;
        $this->etAlSubsequentUseFirst   =   '';
        $this->etAlUseLast              =   false;

        $this->form             =   'long';
        $this->initialize       =   true;
        $this->initializeWith   =   '';
        $this->nameAsSortOrder  =   '';
        $this->sortSeparator    =   ', ';
        $this->nameParts        =   array();

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);

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
                    $this->etAlUseFirst  =   (string) $value;
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin  =   (int) $value;
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst  =   (string) $value;
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast  =   $value == 'true' ? true : false;
                    break;
                case 'form':
                    $this->form  =   (string) $value;
                    break;
                case 'initialize':
                    $this->initialize  =   $value == 'true' ? true : false;
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

        foreach ($xml->children() as $child) {
            if ($child->getName() == 'name-part') {
                $this->nameParts[]  =   new NamePart($child);
            }
        }
    }

    /**
     * Applys the context configuration to the object.
     *
     * @return \Geissler\CSL\Interfaces\Contextualize
     */
    public function apply()
    {
        foreach (Container::getContext()->getOptions() as $name => $value) {
            if (property_exists($this, $name) == true) {
                $this->$name    =   $value;
            }
        }
        return $this;
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

        $etAl   =   false;
        if ($this->etAlMin > 0
            && $length > 1
            && $this->etAlMin >= $length) {
            $etAl   =   true;
        }

        $namesAndSplitter   =   array();
        for ($i = 0; $i < $length; $i++) {
            $namesAndSplitter[] =   $names[$i];

            if ($etAl == true
                && $i == $this->etAlUseFirst - 1) {

                switch ($this->delimiterPrecedesEtAl) {
                    case 'contextual':
                        if ($this->etAlUseFirst > 2) {
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
            if ($i == $length - 2) {
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
                }

                if ($this->and !== '') {
                    $namesAndSplitter[] =   ' ';
                    if ($this->and == 'text') {
                        $namesAndSplitter[] = Container::getLocale()->getTerms('and');
                    } else {
                        if (Container::getLocale()->getTerms('symbol') !== null) {
                            $namesAndSplitter[] =   Container::getLocale()->getTerms('symbol');
                        } else {
                            $namesAndSplitter[] = '&#38;';
                        }
                    }
                    $namesAndSplitter[] =   ' ';
                }
            }
        }

        $return =   str_replace('  ', ' ', implode('', $namesAndSplitter));
        $return =   $this->formating->render($return);
        return $this->affix->render($return);
    }

    /**
     * Formats the name at a postion.
     *
     * @param array $data
     * @param position $position
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
                && $this->initializeWith !== '') {

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
        }

        // format name-parts
        if (count($this->nameParts) > 0) {
            foreach ($this->nameParts as $namePart) {
                $names[$namePart->getName()] =   $namePart->render($names);
            }

            $names['suffix']  =   '';
            $names['non-dropping-particle']  =   '';
            $names['dropping-particle']  =   '';
        }

        $return = implode(' ', $names);

        if ($sort['switched'] == true) {
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
                        && $position == 1)
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
}
