<?php

namespace BotCredifintech\Conversations\Instituciones\Pemex;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__."/ConstantesPemex.php";
require_once __DIR__ . "/../../../prospectos/ProspectoPemex.php";
require_once __DIR__ . "/../../../curlwrap_v2.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BOtCredifintech\Prospectos\ProspectoPemex;


class PemexConversation extends Conversation {

  protected $prospecto, $pPemex;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pPemex = new ProspectoPemex();
      $this->pPemex->nombre = $prospecto->nombre;
      $this->pPemex->telefono = $prospecto->telefono;
      $this->pPemex->email = $prospecto->email;
      $this->pPemex->identificacion = $prospecto->identificacion;
      $this->pPemex->monto = $prospecto->monto;
      $this->pPemex->id = $prospecto->id;
  }

  protected $errores = 0;

  public function error(){
    $this->errores += 1;
    if($this->errores >= 3){
      $this->llamarAsesor();
    } else {
      $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
      $this->askCategoria();
    }
  }

  public function askRequerimientos($pp){

    $conversations = [];

    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
      $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
      $this->say("Tres recibos de pago");
      $this->ask($question, function (Answer $answer) use ($pp){
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $this->askInformacion($pp);
        }
      } else {
        $this->error();
      }
    });
  
  }

  public function askPertenencia($pp){
    $texto = "Para recibir asesoría adecuada te informamos que únicamente atendemos jubilados PEMEX";
    $question = Question::create($texto)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_pertenencia_pemex')
        ->addButtons([
            Button::create("Si, me interesa")->value("Si"),
            Button::create("No, gracias")->value("No"),
        ]);
    $this->ask($question, function(Answer $answer) use ($pp) {
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Si"){
          $this->askInformacion($pp);
        } else {
          $this->bot->startConversation(new SalidaConversation());
        }
      } else {
        $this->error();
      }
    });

  }

  public function askInformacion($pp){
    $this -> askInformePago($pp); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }

  public function askInformePago($pp)
  {
    $this->askForImages("Tome una foto a sus últimos tres informes de pago, envíelas en grupo", function ($images) use ($pp){
      $pp->informeDePago = $images;    
      $i = 1;
      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        
        $note = array(
          "subject"=>"Informe de pago N.". $i,
          "description"=>$url,
          "contact_ids"=>array($pp->id),
        );
        $i++;
        $note = json_encode($note);
        curl_wrap("notes", $note, "POST", "application/json");

      }
      $this->askTerminar(); 
    });
  }


  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      
    });
  }

  public function run(){
    $pp = $this->pPemex;
    $this -> askRequerimientos($pp);
  }

}

?>