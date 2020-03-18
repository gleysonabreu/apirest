<?php
namespace GLEYSON;

class Model
{
  private $values = [];

  public function setData($data)
  {
    
    foreach($data as $key => $value){
      $this->{"set".$key}($value);
    }

  }

    function __call($name, $args)
    {

      $method = substr($name, 0, 3);
      $fieldName = lcfirst(substr($name, 3, strlen($name)));

      if(in_array($fieldName, $this->fields)){
        switch($method){
          case "get":
                  return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
          break;
          case "set":
                  return $this->values[$fieldName] = $args[0];
          break;
        }
      }

    }

    public function getValues()
    {
      return $this->values;
    }

}