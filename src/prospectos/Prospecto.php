<?php

namespace BotCredifintech\Prospectos;

class Prospecto {

  protected $nombre;
  protected $telefono;
  protected $email;
  protected $monto;
  protected $identificacion;
  protected $tipo;

  static function constructWithInfo($nombre, $telefono, $email, $monto, $identificacion){
    $object = new Prospecto();
    $object->nombre = $nombre;
    $object->telefono = $telefono;
    $object->identificacion = $identificacion;
    $object->email = $email;
    $object->monto = $monto;
    return $object;
  }

  public function __set($property, $value){
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
    return $this;
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

}

?>