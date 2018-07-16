<?php

namespace BotCredifintech\Conversations\Instituciones\Gobierno;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__."./ConstantesGobierno.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Instituciones\Gobierno\ConstantesGobierno;


class GobiernoConversation extends Conversation {

  protected $nombre, $telefono, $nss;
  protected $imagenINE, $imagenInformePago;

  protected $errores = 0;
  protected $estadoSeleccionado = "";

  public function error(){
    $this->errores += 1;
    if($this->errores >= 3){
      $this->llamarAsesor();
    } else {
      $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
      $this->askCategoria();
    }
  }

  public function askEstados(){

    $estados = ConstantesGobierno::$estados;

    //Crea el arreglo de opciones de botones de estados disponibles.
    $buttonArray = array();
    foreach($estados as $e){
      array_push($buttonArray, Button::create($e)->value($e));
    }
    array_push($buttonArray, Button::create("Otro")->value("Otro"));

    $question = Question::create("¿De cuál de los siguientes estados es parte la dependencia en la que labora?")
        ->fallback('Si no pertenece a alguna de las anteriores categorías no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_lista_estados')
        ->addButtons($buttonArray);

    $this->ask($question, function (Answer $answer) use ($estados) {
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue();
        if(in_array($selectedValue, $estados)){
          $this -> $estadoSeleccionado = $selectedValue;
          $this->askRequerimientos();
        } else {
          $this->bot->startConversation(new SalidaConversation());
        }
      } else {
        $this->error();
      }
    });
  }

  public function askRequerimientos(){

    $conversations = [];

    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
      $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
      $this->say("Credencial INE/IFE y dos recibos de pago");
      $this->ask($question, function (Answer $answer) use ($tipo){
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $this->askInformacion();
        }
      } else {
        $this->error();
      }
    });
  
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
      $this-> askINE();
    });
  }

  public function askINE()
  {
    $this->askForImages(Constantes::PEDIR_INE, function ($images) {
        $this -> $imagenINE = $images;
        $this->askInformePago();
    });
  }

  public function askInformePago()
  {
    $this->askForImages("Tome una foto a sus últimos dos recibos de pago, envíelas de preferencia en grupo", function ($images) {
      $this->$imagenInformePago = $images;    
      $this->askTerminar(); 
    });
  }


  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run(){
    $this -> askEstados();
  }

}

?>