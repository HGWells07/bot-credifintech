<?php
//Este archivo importa todos los drivers necesarios para imoprtarlos con "use"
require __DIR__ . '/vendor/autoload.php';

//Importando archivos necesarios
require_once __DIR__."./src/Constantes.php";
require_once __DIR__."./src/conversations/instituciones/TipoInstitucionConversation.php";
require_once __DIR__."./src/conversations/MenuConversation.php";
require_once __DIR__."./src/conversations/SalidaConversation.php";

//Configurando namespace de clases de botman (Plantillas de facebook)
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;

use BotMan\BotMan\Drivers\DriverManager;

use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;

use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Cache\DoctrineCache;
use Doctrine\Common\Cache\FilesystemCache;

//Configurando namespace de clases personalizadas
use BotCredifintech\Constantes;
use BotCredifintec\Conversations\Instituciones\TipoInsitucionConversation;
use BotCredifintech\Conversations\MenuConversation;
use BotCredifintech\Conversations\SalidaConversation;

//Cargando Driver de chatbot para Facebook
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

$config = [
  'facebook'=> [
      'token' => 'EAAFamui1cCoBAGbrNG3AX6g1q36eIwGfZAEg9AH3FZAD8pSFwVMpKXOPeUdg5dbcuytXf2fyHD3UyjlGEepSyoLJKPY8iKTaSPAlpxTl1nY31gop0ZChIoyaZCIatBY44SysqrHJbx3FYVamqeKshMKX6iiHBngrGK56C2fi3QZDZD',
      'app_secret' => 'c69036db967a97517be694473c59b8f2', 
      'verification'=>'23894sdf980sf9sdf9d2jaibaveracruzana2000',
  ]
];

$doctrineCacheDriver = new FilesystemCache(__DIR__);
$botman = BotManFactory::create(Constantes::CONFIG, new DoctrineCache($doctrineCacheDriver));

//INICIO DE LA CONVERSACIÓN

$botman->hears('^(?!.*\basesor|ASESOR|Asesor\b).*$', function (BotMan $bot) {
  $nombre = $bot->getUser()->getFirstName();
  $bot -> typesAndWaits(1);
  $bot -> reply("Bienvenido $nombre. Somos una empresa que se dedica a dar créditos a los sectores de gobierno, salud y de secretaría de educación pública");
  $bot -> typesAndWaits(1);
  $bot -> startConversation(new MenuConversation($nombre));
});

$botman->hears('.*(Menu|menu|Menú|MENU|menú).*', function(BotMan $bot) {
	
})->stopsConversation();

$botman->hears('.*(asesor|ASESOR|Asesor).*', function(BotMan $bot) {
    $bot->reply(Constantes::MENSAJE_AYUDA_ASESOR);
})->stopsConversation();

//Mensaje de SALIR
/*
$botman->hears('Salir', function(BotMan $bot){
  $nombre = $bot->getUser()->getFirstName();
  $bot->reply(ButtonTemplate::create('Estimado '.$nombre.', si necesitas mas información de tu crédito sigo a tus ordenes, Gracias !')
      ->addButton(ElementButton::create('¡No gracias!')->type('postback')->payload('¡No gracias!, salir'))
      ->addButton(ElementButton::create('Si, volver al inicio')->type('postback')->payload('Si, volver al inicio'))
  );
});
*/

//Mensaje de AGRADECIMIENTO
/*
$botman->hears('No', function(BotMan $bot){
  $nombre = $bot->getUser()->getFirstName();
  $bot->reply("Es un gusto atenderte ".$nombre.".");
  $bot->startConversation(new SalidaConversation());
});

$botman->hears('Si', function(BotMan $bot){
  $bot->startConversation(new MenuConversation());
});
*/

//Gobierno
/*
$botman->hears('Instituciones de gobierno', function(BotMan $bot){
  $advertencia = "Revisa solo las que tenemos";
  $bot->reply("Selecciona una de las opciones de abajo:");
  $bot->reply(GenericTemplate::create()
      ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
      ->addElements([
          Element::create('Instituciones de Salud')
              ->subtitle($advertencia)
              ->image('https://raw.githubusercontent.com/HGWells07/imagenes/master/credifintech-bot/imagenes-01.jpg')
              ->addButton(ElementButton::create('Ver Instituciones')
                  ->payload('Ver Instituciones de salud')->type('postback')),
          
          Element::create('Instituciones de Educación')
              ->subtitle($advertencia)
              ->image('https://raw.githubusercontent.com/HGWells07/imagenes/master/credifintech-bot/imagenes-02.jpg')
              ->addButton(ElementButton::create('Ver Instituciones')
                  ->payload('Ver Instituciones de educación')->type('postback')),
          
          Element::create('Instituciones del Gobierno')
              ->subtitle($advertencia)
              ->image('https://raw.githubusercontent.com/HGWells07/imagenes/master/credifintech-bot/imagenes-03.jpg')
              ->addButton(ElementButton::create('Ver Instituciones')
                  ->payload('Ver Instituciones del gobierno')->type('postback')),
      ])
  );
});
*/
$botman->listen();
//echo "This is botman running";

?>