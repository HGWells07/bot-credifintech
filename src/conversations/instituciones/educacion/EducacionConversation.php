<?php

namespace BotCredifintech\Conversations\Instituciones\Educacion;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./ConstantesEducacion.php";
require_once __DIR__ . "./../../SalidaConversation.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Instituciones\Educacion\ConstantesEducacion;

class EducacionConversation extends Conversation {

  protected $errores = 0;

  protected $nombre, $telefono, $email, $seccionSindical, $plazoSeleccionado;
  protected $imagenINE, $imagenInformePago;

  public function error(){
    $this->errores += 1;
    if($this->errores >= 3){
      $this->llamarAsesor();
    } else {
      $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
      $this->askCategoria();
    }
  }

  public function askSeccionSindical(){
    $this -> ask("¿En qué sección sindical estás afiliado?", function(Answer $response){
      $this->seccionSindical = $response->getText();
      $this-> askInformacion();
    });
  }

  public function askPlazo(){

    $plazos = ConstantesEducacion::$plazos;

    //Crea el arreglo de opciones de botones de plazos disponibles.
    $buttonArray = array();
    foreach($plazos as $e){
      array_push($buttonArray, Button::create($e)->value($e));
    }
    array_push($buttonArray, Button::create("Otro")->value("Otro"));

    $question = Question::create("¿A cuántos plazos realizará su prestamo?")
        ->fallback('Si no pertenece a alguna de las anteriores categorías no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_lista_plazos')
        ->addButtons($buttonArray);

    $this->ask($question, function (Answer $answer) use ($plazos) {
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue();
        if(in_array($selectedValue, $plazos)){
          $this -> $plazoSeleccionado = $selectedValue;
          $this->askINE();
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
      $this-> askPlazo();
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
    $this->askForImages("Tome una foto a sus últimos tres recibos de pago, envíelas de preferencia en grupo", function ($images) {
        $this->askTerminar(); 
    });
  }

  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run(){
    $this -> askSeccionSindical();
  }

}

?>