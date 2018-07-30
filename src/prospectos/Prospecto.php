<?php

namespace BotCredifintech\Prospectos;

class Prospecto {

  protected $nombre;
  protected $telefono;
  protected $email;
  protected $monto;
  protected $identificacion;

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