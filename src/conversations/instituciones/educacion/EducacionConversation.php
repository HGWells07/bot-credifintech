<?php

namespace BotCredifintech\Conversations\Instituciones\Educacion;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/ConstantesEducacion.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__ . "/../../../prospectos/ProspectoEducacion.php";

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
use BotCredifintech\Prospectos\ProspectoEducacion;

class EducacionConversation extends Conversation {

  protected $errores = 0;

  protected $nombre, $telefono, $email, $seccionSindical, $plazoSeleccionado;
  protected $prospecto, $pEducacion;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pEducacion = new ProspectoEducacion();
      $this->pEducacion->nombre = $prospecto->nombre;
      $this->pEducacion->telefono = $prospecto->telefono;
      $this->pEducacion->email = $prospecto->email;
      $this->pEducacion->identificacion = $prospecto->identificacion;
      $this->pEducacion->monto = $prospecto->identificacion;
  }

  public function error(){
    $this->errores += 1;
    if($this->errores >= 3){
      $this->llamarAsesor();
    } else {
      $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
      $this->askCategoria();
    }
  }

  public function askSeccionSindical($pe){
    $this -> ask("¿En qué sección sindical estás afiliado?", function(Answer $response){
      $pe->seccionSindical = $response->getText();
      $this-> askInformacion($pe);
    });
  }

  public function askPlazo($pe){

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

    $this->ask($question, function (Answer $answer) use ($plazos, $pe) {
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue();
        if(in_array($selectedValue, $plazos)){
          $pe->plazoSeleccionado = $selectedValue;
          $this->askInformePago($pe);
        } else {
          $this->bot->startConversation(new SalidaConversation());
        }
      } else {
        $this->error();
      }
    });
  }

  public function askRequerimientos($pe){

    $conversations = [];

    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
      $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
      $this->say("Dos recibos de pago");
      $this->ask($question, function (Answer $answer) use ($tipo, $pe){
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $this->askInformacion($pe);
        }
      } else {
        $this->error();
      }
    });
  
  }

  public function askInformacion($pe){
    $this -> askPlazo($pe); 
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
    $pe = $this->pEducacion;
    $this -> askSeccionSindical($pe);
  }

}

?>