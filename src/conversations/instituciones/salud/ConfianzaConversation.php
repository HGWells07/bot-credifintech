<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";
require_once __DIR__ . "./../../../prospectos/PropspectoSaludConfianza.php";
//require_once __DIR__ . "/../../../curlwrap_v2.php";
require_once __DIR__ . "/../../../crm/createLead.php";
require_once __DIR__ . "/../../../generico/obtenerListaImagenes.php";

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
      //$this->pConfianza->id = $prospecto->id;
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
      $pc->matricula = $response->getText();
      $this-> askInformePago($pc);
    });
  }

  public function askInformePago($p)
  {
    $this->askForImages(Constantes::PEDIR_TALON_NOMINA, function ($images) use ($p){
        $p->informeDePago = obtenerListaImagenes($images);

        $params = array(
          'firstName' => $p->nombre, 
          'lastName' => $p->apellido, 
          'emailAddress' => $p->email,
          'mobilePhoneNumber' => $p->telefono,
          'companyName' => 'IMSS CONFIANZA',
          'description'=>"
            Monto: $p->monto, \n
            INE: $p->identificacion, \n
  
            Matricula: $p->matricula, \n
            Talon de nomina: $p->informeDePago, \n
            Delegacion: $p->delegacion, \n
          "
        );  
  
        $result = createLead($params);
        $this->say($result);

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