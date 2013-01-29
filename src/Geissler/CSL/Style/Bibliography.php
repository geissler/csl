<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Sorting\Sort;
use Geissler\CSL\Rendering\Layout;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;
use Geissler\CSL\Options\ReferenceGrouping;
use Geissler\CSL\Options\Bibliography as BibliographyOptions;

/**
 * Bibliography.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Bibliography implements Renderable
{
    /** @var Layout **/
    private $layout;
    /** @var Sort **/
    private $sort;
    /** @var bool */
    private $doNotSort;

    /**
     * Parses the Bibliography configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->doNotSort    =   false;

        // init child elements
        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'layout':
                    $this->layout   =   new Layout($child);
                    break;
                case 'sort':
                    $this->sort     =   new Sort($child);
                    break;
            }
        }

        // set Bibliography-specific Options
        $this->layout->setOptions(new BibliographyOptions($xml));

         // set global options and inheritable name options
        $options    =   new Options();
        $options->set('bibliography', $xml);
    }

    /**
     * De-/Activate the sorting option.
     *
     * @param boolean $doNotSort
     * @return \Geissler\CSL\Style\Bibliography
     */
    public function setDoNotSort($doNotSort)
    {
        $this->doNotSort = $doNotSort;
        return $this;
    }

    /**
     * Sort the input data by the rules for bibliographies.
     *
     * @return bool
     */
    public function sort()
    {
        if (isset($this->sort) == true
            && $this->doNotSort == false) {
            $name   =   'bibliography';
            if (Container::getContext()->getName() == 'citation') {
                $name   =   'citation';
            }

            Container::getContext()->setName('bibliography');
            Container::getContext()->enter('bibliography');
            $return =   $this->sort->sort('bibliography');
            Container::getContext()->leave();
            Container::getContext()->setName($name);

            return $return;
        }

        return false;
    }

    /**
     * Render a bibliography.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        // sort
        $this->sort();

        // render citation to create year-suffix if necessary
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true) {
            Container::getContext()->setName('citation');
            Container::getData()->moveToFirst();
            Container::getCitation()->render($data);
            Container::getContext()->setName('bibliography');
        }

        // render
        Container::getContext()->enter('bibliography');
        $result =   $this->layout->render($data);
        Container::getContext()->leave();

        // format return
        $return =   '<div class="csl-bib-body"><div class="csl-entry">';
        if (is_array($result) == true
            && count($result) > 0) {
            $return .=  implode('</div><div class="csl-entry">', $result);
        } else {
            $return .=  $result;
        }

        return $return . '</div></div>';
    }

    /**
     * Add additional options for displaying a bibliography.
     *
     * @param array $result
     * @return array
     */
    private function addOptions($result)
    {
        $length =   count($result);

        for ($i = 0; $i < $length; $i++) {
            if (Container::getContext()->getValue('hangingIndent', 'bibliography') == true) {

            } elseif (Container::getContext()->getValue('secondFieldAlign', 'bibliography') == 'flush') {
                $result[$i] =   '<div class="csl-left-margin">'
                    . preg_replace('/( )/', ' </div><div class="csl-right-inline">', $result[$i], 1)
                    . '</div>';
            }
        }

        return $result;
    }
}
