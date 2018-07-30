<?php

namespace BotCredifintech\Conversations;

require __DIR__ . './../../vendor/autoload.php';

require_once __DIR__ . "/../Constantes.php";
require_once __DIR__ . "/../prospectos/Prospecto.php";
require_once __DIR__ . "/instituciones/salud/SaludConversation.php";
require_once __DIR__ . "/instituciones/gobierno/GobiernoConversation.php";
require_once __DIR__ . "/instituciones/educacion/EducacionConversation.php";
require_once __DIR__."/SalidaConversation.php";

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

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;

class SolicitarDatosConversation extends Conversation{

  private $selectedValue;
  private $prospecto;

  public function __construct($tipo)
  {
      $this->selectedValue = $tipo;
      $this->prospecto = new Prospecto();
  }

  public function askInformacion(){
    $this -> askNombre(); 
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
  public function askNombre(){
    $this -> ask(Constantes::PEDIR_NOMBRE, function(Answer $response){
      $nombre = $response->getText();
      $this->$prospecto->nombre = $nombre;
      $this-> askTelefono();
    });
  }

  public function askTelefono(){
    $this -> ask(Constantes::PEDIR_TELEFONO, function(Answer $response){
      $telefono = $response->getText();
      $this->$prospecto->telefono = $telefono;
      $this-> askEmail();
    });
  }

  public function askEmail(){
    $this -> ask(Constantes::PEDIR_EMAIL, function(Answer $response){
      $email = $response->getText();
      $this->$prospecto->email = $email;
      $this-> askMonto();
    });
  }

  public function askMonto(){
    $this -> ask(Constantes::PEDIR_MONTO, function(Answer $response){
      $monto = $response->getText();
      $this->$prospecto->email = $email;
      $this-> askINE();
    });
  }

  public function askINE(){
    $this->askForImages(Constantes::PEDIR_INE, function ($images) {
      if($this->$selectedValue=="Area/Salud"){
        $this->bot->startConversation(new SaludConversation());
      }
      if($this->$selectedValue=="Area/Educación"){
        $this->bot->startConversation(new EducacionConversation());
      }
      if($this->$selectedValue=="Area/Gobierno"){
        $this->bot->startConversation(new GobiernoConversation());
      }
      if($this->$selectedValue=="Area/Ninguna"){
        $this->bot->startConversation(new SalidaConversation());
      }
    });
  }

  public function run() {
    $this -> askInformacion();
  }

}