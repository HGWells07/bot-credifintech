<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";
require_once __DIR__ . "/../../../prospectos/ProspectoSaludParitaria.php";
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
use BotCredifintech\Prospectos\ProspectoSaludParitaria;

class ParitariaConversation extends Conversation
{
  protected $prospecto, $pParitaria;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pParitaria = new ProspectoSaludParitaria();
      $this->pParitaria->nombre = $prospecto->nombre;
      $this->pParitaria->telefono = $prospecto->telefono;
      $this->pParitaria->email = $prospecto->email;
      $this->pParitaria->identificacion = $prospecto->identificacion;
      $this->pParitaria->monto = $prospecto->monto;
      $this->pParitaria->id = $prospecto->id;
  }

  public function askInformacion(){
    $pp = $this->pParitaria;
    $this -> askMatricula($pp); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }

  public function askMatricula($pp){
    $this -> ask(Constantes::PEDIR_MATRICULA, function(Answer $response) use ($pp){
      $pp->matricula = $response->getText();
      $this-> askInformePago($pp);
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
        'companyName' => 'IMSS JUBILADO',
        'description'=>"
          Monto: $p->monto, \n
          INE: $p->identificacion, \n

          Matricula: $p->matricula, \n
          Talon de nomina: $p->informeDePago, \n
          Delegación: $p->delegacion, \n
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