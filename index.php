<?php
//Este archivo importa todos los drivers necesarios para imoprtarlos con "use"
require __DIR__ . '/vendor/autoload.php';

//Importando archivos necesarios
require_once __DIR__."/src/Constantes.php";
require_once __DIR__."/src/conversations/instituciones/TipoInstitucionConversation.php";
require_once __DIR__."/src/conversations/MenuConversation.php";
require_once __DIR__."/src/conversations/SalidaConversation.php";

//Extra Facebook Drivers
//require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookDriver.php";
require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookImageDriver.php";
require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookFileDriver.php";

//Configurando namespace de clases de botman (Plantillas de facebook)
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;

use BotMan\BotMan\Drivers\DriverManager;

use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\Drivers\Facebook\Extensions\Element;

use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Cache\DoctrineCache;
use Doctrine\Common\Cache\FilesystemCache;

//Configurando namespace de clases personalizadas
use BotCredifintech\Constantes;
use BotCredifintec\Conversations\Instituciones\TipoInsitucionConversation;
use BotCredifintech\Conversations\MenuConversation;
use BotCredifintech\Conversations\SalidaConversation;

//Ca,
// Driver de chatbot para Facebook
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookImageDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookFileDriver::class);

$config = [
  
  'facebook'=> [
    //Botencio McBot
    'token' => Constantes::generateTokenArray()["T_BOTENCIO"],
    'app_secret' => Constantes::APP_SECRET, 
    'verification'=> Constantes::VERIFICATION,
  ],
  /*
  'facebook'=> [
    //Botencio McBot
    'token' => Constantes::generateTokenArray()["T_CF"],
    'app_secret' => Constantes::APP_SECRET, 
    'verification'=> Constantes::VERIFICATION,
  ],
  */
  /*
  'facebook'=> [
    //Credifintech
    'token' => 'EAAGrT16HtJgBANcy1trAD3kht0pIoW18gHaaUY9DcXjsTGBfifvKxXEhtGox1yd6iWqRlpiAKrTxwmM9Ow1I71x7ZBI0OOFgsxuXD3rx1bxk55NlovIwJAoi5EWpNGYsMRDcKurUZCL2EWxen8fWZCCX9L6c7S2eHiFt0ZC8eZA4qVHym8yOT',
    'app_secret' => 'e4647b87a6b18da6803bddc3b3349674', 
    'verification'=>'d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852',
  ]
  */
];

$doctrineCacheDriver = new FilesystemCache(__DIR__);
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

$botman->hears('^(?!.*\basesor|ASESOR|Asesor\b).*$', function (BotMan $bot) {
  //$nombre = $bot->getUser()->getUsername();
  $nombre = "";
  $bot -> reply("Bienvenido$nombre. Somos una empresa que se dedica a dar créditos a los sectores de gobierno, IMSS y de la SEP");
  $bot -> startConversation(new MenuConversation($nombre));
});

$botman->hears('.*(Menu|menu|Menú|MENU|menú).*', function(BotMan $bot) {
	
})->stopsConversation();

$botman->hears('.*(asesor|ASESOR|Asesor).*', function(BotMan $bot) {
    $bot->reply(Constantes::MENSAJE_AYUDA_ASESOR);
})->stopsConversation();

$botman->listen();
//echo "This is botman running";

?>