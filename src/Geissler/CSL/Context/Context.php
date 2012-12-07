<?php
namespace Geissler\CSL\Context;

/**
 * Stores all globaly changeable options to override local configurations.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Context
{
    /** @var string **/
    private $name;
    /** @var array **/
    private $style;
    /** @var array **/
    private $citation;
    /** @var array **/
    private $bibliography;

    /**
     * Inits the arrays.
     */
    public function __construct()
    {
        $this->style        =   array();
        $this->citation     =   array();
        $this->bibliography =   array();
    }

    /**
     * Set mode (citation or bibliography).
     *
     * @param string $name
     * @return \Geissler\CSL\Context\Context
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the rendering context (citation or bibliography).
     *
     * @return stirng
     */
    public function getName()
    {
        return $this->name;
    }

    public function addStyle($name, $value)
    {
        $this->style[$name] =   $value;
        return $this;
    }

    public function addCitation($name, $value)
    {
        $this->citation[$name] =   $value;
        return $this;
    }

    public function addBibliography($name, $value)
    {
        $this->bibliography[$name] =   $value;
        return $this;
    }

    /**
     * Retrieve a single value from a context.
     *
     * @param string $name name in camelCase and without -
     * @param string $context style, citation or bibliography
     * @return string
     */
    public function getValue($name, $context = 'style')
    {
        switch ($context) {
            case 'style':
                if (array_key_exists($name, $this->style) == true) {
                    return $this->style[$name];
                }
                break;
            case 'citation':
                if (array_key_exists($name, $this->citation) == true) {
                    return $this->citation[$name];
                }
                break;
            case 'bibliography':
                if (array_key_exists($name, $this->bibliography) == true) {
                    return $this->bibliography[$name];
                }
                break;
        }

        return '';
    }

    /**
     * Returns the standard values for this context.
     *
     * @return  array
     */
    public function getOptions()
    {
        if ($this->name == 'citation') {
            return array_merge($this->citation, $this->style);
        } elseif ($this->name == 'bibliography') {
            return array_merge($this->bibliography, $this->style);
        }

        throw new \ErrorException('No rendering context defined!');
    }
}
