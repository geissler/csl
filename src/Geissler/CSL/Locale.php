<?php
namespace Geissler\CSL;

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

    /**
     * Specifies the path where the locales- files are located.
     *
     * @param string $dir
     * @return \Geissler\CSL\Locale
     */
    public function setDir($dir)
    {
        $this->dir = __DIR__ . '/../../../' . $dir;
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
        $this->parseXml(new \SimpleXMLElement($content));

        return $this;
    }

    /**
     * Parses the language options from the xml element.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Locale
     */
    public function readXml(\SimpleXMLElement $xml)
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

        throw new \ErrorException('Option (' . $value . ') not set!');
    }

    public function getDate($form, $part = '')
    {
        return $this->date;
    }

    /**
     * Retrieve a term value.
     *
     * @param string $name
     * @param string $form optional form parameter
     * @param string $type single or multiple
     * @return string
     * @throws \ErrorException
     */
    public function getTerms($name, $form = '', $type = 'single')
    {
        $length =   count($this->terms);

        for ($i = 0; $i < $length; $i++) {
            if ($this->terms[$i]['name'] == $name
                && $this->terms[$i]['form'] == $form) {
                    return $this->terms[$i][$type];
            }
        }

        throw new \ErrorException(
                            'The locale option (name = ' . $name . ', form = ' . $form
                            . ', type = ' . $type . 'is not set');
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
        }
        elseif (array_key_exists($language, $this->primaryDialect) == true) {
            return $this->primaryDialect[$language];
        }

        return 'en-US';
    }

    /**
     * Parses the content of the xml file into the internal propertys.
     *
     * @param \SimpleXMLElement $xml
     */
    private function parseXml(\SimpleXMLElement $xml)
    {
        $this->terms    =   array();
        $this->options  =   array();
        $this->date     =   array();

        foreach ($xml as $node) {
            switch($node->getName()) {
                case 'style-options':
                    foreach ($node->attributes() as $name => $value) {
                        if ((string) $value == 'true') {
                            $this->options[$name]   =   true;
                        }
                        else {
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
                        $date   =   array();
                        foreach ($node->children() as $child) {
                            foreach ($child->attributes() as $attribute => $value) {
                                $date[$attribute]    =   (string) $value;
                            }
                        }

                        $this->date[(string) $form]  =   $date;
                    }
                break;
            }
        }
    }
}
