<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "./../../../Constantes.php";
require_once __DIR__ . "./../../SalidaConversation.php";
require_once __DIR__ . "/PensionadosConversation.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Instituciones\Salud\PensionadosConversation;

class SaludConversation extends Conversation
{

  const PENSIONADOS = "Pensionados";
  const CONFIANZA = "Confianza";
  const JUBILADOS = "Jubilados";
  const PARITARIA = "Paritaria";

  protected $errores = 0;

  public function askCategoria(){

    $question = Question::create("¿En que categoría se encuentra usted?")
        ->fallback('Si no pertenece a alguna de las anteriores categorías no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_area_gobierno')
        ->addButtons([
            Button::create(self::PENSIONADOS)->value(self::PENSIONADOS),
            Button::create(self::CONFIANZA)->value(self::CONFIANZA),
            Button::create(self::JUBILADOS)->value(self::JUBILADOS),
            Button::create(self::PARITARIA)->value(self::PARITARIA),
        ]);

    $this->ask($question, function (Answer $answer) {
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue();
        if($selectedValue==self::PENSIONADOS){
          $this->bot->startConversation(new PensionadosConversation());
        }
        if($selectedValue==self::CONFIANZA){
          $this->say('Confianza');
        }
        if($selectedValue==self::JUBILADOS){
          $this->say('Jubilados');
        }
        if($selectedValue==self::PARITARIA){
          $this->say('Paritaria');
        }
      } else {
        $this->errores += 1;
        if($this->errores >= 3){
          $this->llamarAsesor();
        } else {
          $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
          $this->askCategoria();
        }
        
    }
    });
  }

  public function llamarAsesor(){
    $this->say(Constantes::MENSAJE_AYUDA_ASESOR);
  }

  public function stopsConversation(IncomingMessage $message)
	{
		if (strcasecmp($message->getText(), 'asesor') == 0) {
      $this->say("La conversación se ha detenido, espere al asesor");
			return true;
		}
		return false;
	}

  public function run(){
    $this->askCategoria();
  }
}

?>