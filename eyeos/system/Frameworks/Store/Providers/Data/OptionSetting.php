<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 16:52
 */

class OptionSetting
{
    private $name;
    private $value;

    public function __construct($name=NULL,$value=NULL){
        if($name) $this->name = $name;
        if($value) $this->value = $value;
    }

    /**
     * @param mixed $option
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}

?>