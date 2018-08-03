<?php

namespace BotCredifintech\Conversations;

require __DIR__ . './../../vendor/autoload.php';

require_once __DIR__ . "/../Constantes.php";
require_once __DIR__ . "/../prospectos/Prospecto.php";
require_once __DIR__ . "/instituciones/salud/SaludConversation.php";
require_once __DIR__ . "/instituciones/gobierno/GobiernoConversation.php";
require_once __DIR__ . "/instituciones/educacion/EducacionConversation.php";
require_once __DIR__."/SalidaConversation.php";
require_once __DIR__ . "./../curlwrap_v2.php";

use BotCredifintech\Conversations\Instituciones\Salud\SaludConversation;
use BotCredifintech\Conversations\Instituciones\Educacion\EducacionConversation;
use BotCredifintech\Conversations\Instituciones\Gobierno\GobiernoConversation;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Prospectos\Prospecto;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;

class SolicitarDatosConversation extends Conversation{

  private $selectedValue;
  private $prospecto;

  public function __construct($selectedValue)
  {
      $this->selectedValue = $selectedValue;
      $this->prospecto = new Prospecto();
  }

  public function askInformacion(){
    $sv = $this->selectedValue;
    $p = $this->prospecto;
    $this -> askNombre($p, $sv); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }
  
  //Funciones para juntar datos
  public function askNombre($p, $sv){
    $this -> ask(Constantes::PEDIR_NOMBRE, function(Answer $response) use ($p, $sv){
      $nombre = $response->getText();
      $p->nombre = $nombre;
      $this-> askApellido($p, $sv);
    });
  }

  public function askApellido($p, $sv){
    $this -> ask(Constantes::PEDIR_APELLIDO, function(Answer $response) use ($p, $sv){
      $apellido = $response->getText();
      $p->apellido = $apellido;
      $this-> askTelefono($p, $sv);
    });
  }

  public function askTelefono($p, $sv){
    $this -> ask(Constantes::PEDIR_TELEFONO, function(Answer $response) use ($p, $sv){
      $telefono = $response->getText();
      $p->telefono = $telefono;
      $this-> askEmail($p, $sv);
    });
  }

  public function askEmail($p, $sv){
    $this -> ask(Constantes::PEDIR_EMAIL, function(Answer $response) use ($p, $sv){
      $email = $response->getText();
      $p->email = $email;
      $this-> askMonto($p, $sv);
    });
  }

  public function askMonto($p, $sv){   
    $this -> ask(Constantes::PEDIR_MONTO, function(Answer $response) use ($p, $sv){
      $monto = $response->getText();
      $p->monto = $monto;
      $this-> askINE($p, $sv);
    });
  }

  public function askINE($p, $sv) {
    $this->askForImages(Constantes::PEDIR_INE, function ($images) use ($p, $sv) {
      $p->identificacion = $images;
      // Primer guardado de información

      $contact_json =array(
        "properties"=>array(
          array(
            "name"=>"first_name",
            "value"=>$p->nombre,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"last_name",
            "value"=>$p->apellido,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"email",
            "value"=>$p->email,
            "type"=>"SYSTEM"
          ),  
          array(
              "name"=>"phone",
              "value"=>$p->telefono,
              "type"=>"SYSTEM"
          ),
        ),
      );

      $contact_json = json_encode($contact_json);
      curl_wrap("contacts", $contact_json, "POST", "application/json");

      if($sv=="Area/Salud"){
        $this->bot->startConversation(new SaludConversation($p));
      }
      if($sv=="Area/Educación"){
        $this->bot->startConversation(new EducacionConversation($p));
      }
      if($sv=="Area/Gobierno"){
        $this->bot->startConversation(new GobiernoConversation($p));
      }
      if($sv=="Area/Ninguna"){
        $this->bot->startConversation(new SalidaConversation());
      }
    });
  }

  public function run() {
    $this -> askInformacion();
  }

}