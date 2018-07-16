<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Insituciones\SaludConversation;
use BotCredifintech\Conversations\Instituciones\Salud\ConstantesSalud;

class JubiladosConversation extends Conversation
{
  protected $nombre, $telefono, $nss;
  protected $imagenINE, $imagenInformePago;


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
      $this->nombre = $response->getText();
      $this-> askTelefono();
    });
  }

  public function askTelefono(){
    $this -> ask(Constantes::PEDIR_TELEFONO, function(Answer $response){
      $this->telefono = $response->getText();
      $this-> askEmail();
    });
  }

  public function askEmail(){
    $this -> ask(Constantes::PEDIR_EMAIL, function(Answer $response){
      $this->email = $response->getText();
      $this-> askMonto();
    });
  }

  public function askMonto(){
    $this -> ask(Constantes::PEDIR_MONTO, function(Answer $response){
      $this->monto = $response->getText();
      $this-> askNSS();
    });
  }

  public function askNSS(){
    $this -> ask(Constantes::PEDIR_NSS, function(Answer $response){
      $this->nss = $response->getText();
      $this-> askINE();
    });
  }

  public function askINE()
  {
    $this->askForImages(Constantes::PEDIR_INE, function ($images) {
        $this->askInformePago();
    });
  }

  public function askInformePago()
  {
    $this->askForImages(Constantes::PEDIR_INFORME_PAGO, function ($images) {
        $this->askTerminar(); 
    });
  }

  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run() {
    $this -> askInformacion();
  }
}

?>