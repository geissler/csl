<?php
namespace Geissler\CSL\Locale;

/**
 * Parse and access locales propertys.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Locale
{
    /** @var string **/
    private $dir;
    /** @var string **/
    private $file;
    /** @var array **/
    private $primaryDialect;
    /** @var array **/
    private $options;
    /** @var array **/
    private $date;
    /** @var array **/
    private $terms;
    /** @var string **/
    private $language;

    /**
     * Specifies the path where the locales- files are located.
     *
     * @param string $dir
     * @return \Geissler\CSL\Locale
     */
    public function setDir($dir)
    {
        $this->dir = __DIR__ . '/../../../../' . $dir;
        return $this;
    }

    /**
     * Specifies the format of the locales files.
     *
     * @param type $file
     * @return \Geissler\CSL\Locale
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Specifies the mapping of the primary dialects to the main language file.
     *
     * @param string $primaryDialect JSON array
     * @return \Geissler\CSL\Locale
     */
    public function setPrimaryDialect($primaryDialect)
    {
        $this->primaryDialect = json_decode($primaryDialect, true);
        return $this;
    }

    /**
     * Parses the language file.
     *
     * @param string $language
     * @return \Geissler\CSL\Locale
     * @throws \ErrorException  When the file is missing
     */
    public function readFile($language = 'en-US')
    {
        $language   =   $this->determineLanguage($language);
        $file       =   $this->dir . '/' . str_replace('LANGUAGE', $language, $this->file);

        if (file_exists($file) == false) {
            throw new \ErrorException('Locale file (' . $file . ') is missing!');
        }

        $content    =   file_get_contents($file);
        $this->init();
        $this->parseXml(new \SimpleXMLElement($content));
        $this->setLanguage($language);

        return $this;
    }

    /**
     * Parses the language options from the xml element.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Locale
     */
    public function addXml(\SimpleXMLElement $xml)
    {
        $this->parseXml($xml);
        return $this;
    }

    /**
     * Retrieve an option (limit-day-ordinals-to-day-1 or punctuation-in-quote).
     *
     * @param string $value
     * @return boolean
     * @throws \ErrorException Missing option
     */
    public function getOptions($value)
    {
        if (isset($this->options[$value]) == true) {
            return $this->options[$value];
        }

        return null;
    }

    /**
     * Retrieve the date format.
     *
     * @param string $form Format (numeric or text)
     * @return array|null
     */
    public function getDate($form)
    {
        if (isset($this->date[$form]) == true) {
            return $this->date[$form];
        }

        return null;
    }

    /**
     * Retrieve the locale configuration for a date-parte element (day, month, year).
     *
     * @param string $form  text or numeric
     * @param string $name  day, month, year
     * @return string|null
     */
    public function getDateAsXml($form, $name)
    {
        if ($this->getDate($form) !== null) {
            foreach ($this->date[$form] as $datePart) {
                if ($datePart['name'] == $name) {
                    return $datePart['xml'];
                }
            }
        }

        return null;
    }

    /**
     * Retrieve a term value.
     *
     * @param string $name
     * @param string $form Optional form parameter
     * @param string $type Single or multiple
     * @param array $additional Additional parameters to compare
     * @return string|null
     */
    public function getTerms($name, $form = '', $type = 'single', array $additional = array())
    {
        $length =   count($this->terms);

        for ($i = 0; $i < $length; $i++) {
            if ($this->terms[$i]['name'] == $name
                && $this->terms[$i]['form'] == $form) {

                $found = true;
                if (count($additional) > 0) {
                    foreach ($additional as $option => $value) {
                        if (array_key_exists($option, $this->terms[$i]) == false
                            || $this->terms[$i][$option] !== $value) {

                            $found  =   false;
                            break;
                        }
                    }
                }

                if ($found == true) {
                    return $this->terms[$i][$type];
                }
            }
        }

        return null;
    }

    /**
     * Retrieve the language.
     *
     * @return string
     */
    public function getLanguage()
    {
        if (isset($this->language) == true) {
            return $this->language;
        }

        return 'en';
    }

    /**
     * Stores the language.
     *
     * @param string $language
     * @return \Geissler\CSL\Locale\Locale
     */
    private function setLanguage($language)
    {
        $this->language = preg_replace('/(-[A-z]+)$/', '', $language);
        return $this;
    }

    /**
     * Determines the language to use.
     *
     * @param string $language
     * @return string
     */
    private function determineLanguage($language)
    {
        if (preg_match('/^[a-z]{2}\-[A-Z]{2}$/', $language) == 1) {
            return $language;
        } elseif (array_key_exists($language, $this->primaryDialect) == true) {
            return $this->primaryDialect[$language];
        }

        return 'en-US';
    }

    /**
     * Inits the arrays.
     *
     * @return void
     */
    private function init()
    {
        $this->terms    =   array();
        $this->options  =   array();
        $this->date     =   array();
    }

    /**
     * Parses the content of the xml file into the internal propertys.
     *
     * @param \SimpleXMLElement $xml
     */
    private function parseXml(\SimpleXMLElement $xml)
    {
        foreach ($xml as $node) {
            switch($node->getName()) {
                case 'style-options':
                    foreach ($node->attributes() as $name => $value) {
                        if ((string) $value == 'true') {
                            $this->options[$name]   =   true;
                        } else {
                            $this->options[$name]   =   false;
                        }
                    }
                    break;
                case 'terms':
                    foreach ($node->children() as $child) {
                        $term   =   array(
                            'name'      =>  '',
                            'form'      =>  '',
                            'single'    =>  (string) $child,
                            'multiple'  =>  '');

                        foreach ($child->attributes() as $attribute => $value) {
                            $term[$attribute]    =   (string) $value;
                        }

                        foreach ($child->children() as $subChildren) {
                            $term[$subChildren->getName()] =   (string) $subChildren;
                        }

                        $this->terms[]  =   $term;
                    }
                    break;
                case 'date':
                    foreach ($node->attributes() as $form) {
                        $this->date[(string) $form] =   array();

                        foreach ($node->children() as $child) {
                            $date           =   array();
                            $date['xml']    =   $child->asXML();

                            foreach ($child->attributes() as $attribute => $value) {
                                $date[$attribute]    =   (string) $value;
                            }

                            $this->date[(string) $form][]  =   $date;
                        }
                    }
                    break;
            }
        }
    }
}
