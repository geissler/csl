<?php
namespace Geissler\CSL\Context;

/**
 * Stores all globally changeable options to override local configurations and the actual rendering context.
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
    /** @var array */
    private $context;
    /** @var array */
    private $disambiguation;
    /** @var bool */
    private $ignoreEtAlSubsequent;
    private $chooseDisambiguation;
    private $chooseDisambiguateValue;

    /**
     * Init's the arrays.
     */
    public function __construct()
    {
        $this->style                    =   array();
        $this->citation                 =   array();
        $this->bibliography             =   array();
        $this->context                  =   array();
        $this->disambiguation           =   array();
        $this->ignoreEtAlSubsequent     =   false;
        $this->chooseDisambiguation     =   false;
        $this->chooseDisambiguateValue  =   false;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * De-/Activate et-al-subsequent ignoring.
     *
     * @param boolean $ignoreEtAlSubsequent
     * @return Context
     */
    public function setIgnoreEtAlSubsequent($ignoreEtAlSubsequent)
    {
        $this->ignoreEtAlSubsequent = $ignoreEtAlSubsequent;
        return $this;
    }

    /**
     * Activate the usage of choose disambiguate.
     *
     * @return Context
     */
    public function activateChooseDisambiguation()
    {
        $this->chooseDisambiguation =   true;
        return $this;
    }

    /**
     * Check if choose disambiguate is used.
     * @return bool
     */
    public function isChooseDisambiguationActive()
    {
        return $this->chooseDisambiguation;
    }

    /**
     * Set the result of a choose disambiguate.
     *
     * @param $useChooseDisambiguate
     * @return Context
     */
    public function setChooseDisambiguateValue($useChooseDisambiguate)
    {
        $this->chooseDisambiguateValue = $useChooseDisambiguate;
        return $this;
    }

    /**
     * Retrieve the choose disambiguate result.
     *
     * @return bool
     */
    public function getChooseDisambiguateValue()
    {
        return $this->chooseDisambiguateValue;
    }

    /**
     * Add a global style option.
     *
     * @param string $name
     * @param string|boolean|integer $value
     * @return Context
     */
    public function addStyle($name, $value)
    {
        $this->style[$name] =   $value;
        return $this;
    }

    /**
     * Add a citation option.
     *
     * @param string $name
     * @param string|boolean|integer $value
     * @return Context
     */
    public function addCitation($name, $value)
    {
        $this->citation[$name] =   $value;
        return $this;
    }

    /**
     * Adda bibliography option.
     *
     * @param string $name
     * @param string|boolean|integer $value
     * @return Context
     */
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
     * @return array
     * @throws \ErrorException
     */
    public function getOptions()
    {
        if (isset($this->name) == false) {
            throw new \ErrorException('No rendering context defined!');
        }

        if ($this->name == 'citation') {
            $return =   array_merge($this->citation, $this->style);
        } elseif ($this->name == 'bibliography') {
            $return =    array_merge($this->bibliography, $this->style);
        } else {
            $return =   $this->style;
        }

        if ($this->ignoreEtAlSubsequent == true) {
            if (isset($return['etAlSubsequentMin']) == true) {
                unset($return['etAlSubsequentMin']);
            }

            if (isset($return['etAlSubsequentUseFirst']) == true) {
                unset($return['etAlSubsequentUseFirst']);
            }
        }

        return $return;
    }

    /**
     * Enter a specific position in the "CSL rendering tree" like bibliography or sort.
     *
     * @param string $name
     * @param array $options
     * @return Context
     */
    public function enter($name, array $options = array())
    {
        array_push($this->context, array('name' => $name, 'option' => $options));
        return $this;
    }

    /**
     * Leave the actual context.
     *
     * @return Context
     */
    public function leave()
    {
        array_pop($this->context);
        return $this;
    }

    /**
     * Test if in an specific context.
     *
     * @param string $name
     * @return bool
     */
    public function in($name)
    {
        $context =  end($this->context);

        if ($context['name'] == $name) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve a context option from the actual context or the given context.
     *
     * @param string $option
     * @param bool|string $contextName
     * @return null|mixed
     */
    public function get($option, $contextName = false)
    {
        if ($contextName == false) {
            $context =  end($this->context);
        } else {
            $length =   count($this->context);

            for ($i = 0; $i < $length; $i++) {
                if ($this->context[$i]['name'] == $contextName) {
                    $context    =   $this->context[$i];
                    break;
                }
            }
        }

        if (isset($context['option'][$option]) == true) {
            if (is_object($context['option'][$option]) == true) {
                return clone $context['option'][$option];
            } else {
                return $context['option'][$option];
            }
        }

        return null;
    }

    /**
     * Add additional options to disambiguate names.
     *
     * @param string $class
     * @param array $options
     * @return Context
     */
    public function setDisambiguationOptions($class, array $options)
    {
        if ($this->getDisambiguationOptions($class) == false) {
            $this->disambiguation[$class]    =   $options;
        } else {
            $this->disambiguation[$class]    =   array_merge($this->getDisambiguationOptions($class), $options);
        }
        return $this;
    }

    /**
     * Retrieve disambiguation options.
     *
     * @param string $class
     * @return array|bool
     */
    public function getDisambiguationOptions($class)
    {
        if (array_key_exists($class, $this->disambiguation) == true) {
            return $this->disambiguation[$class];
        }

        return false;
    }

    /**
     * Retrieve a disambiguation option.
     *
     * @param string $class
     * @param string $option
     * @return mixed|bool
     */
    public function getDisambiguationOption($class, $option)
    {
        if ($this->getDisambiguationOptions($class) !== false
            && isset($this->disambiguation[$class][$option]) == true) {
            return $this->disambiguation[$class][$option];
        }

        return false;
    }

    /**
     * Remove a disambiguation option.
     *
     * @param string $class
     * @param string $option
     * @return Context
     */
    public function removeDisambiguationOption($class, $option)
    {
        if (isset($this->disambiguation[$class][$option]) == true) {
            unset($this->disambiguation[$class][$option]);
        }

        return $this;
    }

    /**
     * Remove the disambiguation options for a class.
     *
     * @param string $class
     * @return Context
     */
    public function removeDisambiguationOptions($class)
    {
        if (array_key_exists($class, $this->disambiguation) == true) {
            unset($this->disambiguation[$class]);
        }

        return $this;
    }

    /**
     * Remove all disambiguation options.
     *
     * @return Context
     */
    public function clearDisambiguationOptions()
    {
        $this->disambiguation   =   array();
        return $this;
    }
}
