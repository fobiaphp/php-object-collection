<?php
/**
 * ObjectItem class  - ObjectItem.php file
 *
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2014 Dmitriy Tyurin
 */

class ObjectItem
{
    public $name;
    public $key;

    function __construct($name = 'default', $key = null, $keyName = null, $keyValue = null)
    {
        $this->name = $name;
        $this->key = $key;

        if ($keyName !== null) {
            $this->$keyName = $keyValue;
        }

    }

}