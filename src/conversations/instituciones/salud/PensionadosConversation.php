<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__ . "./ConstantesSalud.php";

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

class PensionadosConversation extends Conversation
{
  protected $nombre, $telefono, $nss;
  protected $imagenINE, $imagenInformePago;

  public function askDocumentacion(){
    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
    $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
    $this->say(ConstantesSalud::DATOS_PENSIONADO);
    $this->ask($question, function (Answer $answer) {
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $this->askInformacion();
        }
      } else {
        $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
        $this->askDocumentacion();
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
    $this -> ask("Escriba su nombre completo", function(Answer $response){
      $this->nombre = $response->getText();
      $this-> askTelefono();
    });
  }

  public function askTelefono(){
    $this -> ask("Escriba su número telefónico", function(Answer $response){
      $this->telefono = $response->getText();
      $this-> askNSS();
    });
  }

  public function askNSS(){
    $this -> ask("Escriba su NSS", function(Answer $response){
      $this->nss = $response->getText();
      $this-> askINE();
    });
  }

  public function askINE()
  {
    $this->askForImages('Por favor tome una foto de su INE', function ($images) {
        $this->askInformePago();
    });
  }

  public function askInformePago()
  {
    $this->askForImages('Por favor tome una foto de su Informe de pago', function ($images) {
        $this->askTerminar(); 
    });
  }

  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run() {
    $this -> askDocumentacion();
  }
}

?>