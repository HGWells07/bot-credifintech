<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";
require_once __DIR__ . "./../../../prospectos/PropspectoSaludConfianza.php";
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
use BotCredifintech\Conversations\Insituciones\SaludConversation;
use BotCredifintech\Conversations\Instituciones\Salud\ConstantesSalud;
use BotCredifintech\Prospectos\PropspectoSaludConfianza;

class ConfianzaConversation extends Conversation
{
  protected $prospecto, $pConfianza;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pConfianza = new PropspectoSaludConfianza();
      $this->pConfianza->nombre = $prospecto->nombre;
      $this->pConfianza->telefono = $prospecto->telefono;
      $this->pConfianza->email = $prospecto->email;
      $this->pConfianza->identificacion = $prospecto->identificacion;
      $this->pConfianza->monto = $prospecto->monto;
      $this->pConfianza->id = $prospecto->id;
  }

  public function askInformacion(){
    $pc = $this->pConfianza;
    $this -> askMatricula($pc); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }

  public function askMatricula($pc){
    $this -> ask(Constantes::PEDIR_MATRICULA, function(Answer $response) use ($pc){
      $matricula = $response->getText();
      $note = array(
        "subject"=>"Matricula",
        "description"=>$matricula,
        "contact_ids"=>array($pc->id),
      );
      $note = json_encode($note);
      curl_wrap("notes", $note, "POST", "application/json");
      $this-> askInformePago($pc);
    });
  }

  public function askInformePago($pc)
  {
    $this->askForImages(Constantes::PEDIR_TALON_NOMINA, function ($images) use ($pc){
        $pc->informeDePago = $images;

        $i = 1;
      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        
        $note = array(
          "subject"=>"Talón de nómina N.". $i,
          "description"=>$url,
          "contact_ids"=>array($pc->id),
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
      return false;
    });
  }

  public function run() {
    $this -> askInformacion();
  }
}

?>