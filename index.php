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

//Cargando Driver de chatbot para Facebook
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookImageDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookFileDriver::class);

$config = [
  'facebook'=> [
    'token' => 'EAAGrT16HtJgBAHymAa2BDFna33W2iZB3JUMYnDnebrnk5gdO4WHsTaReBxa935JEX6OfmGNZBH6fpURDJXVuuvPwYoVrDZCZBBcZCYfSBG7z5ph4DwSlRLVVCHEcOfGhk6j3ZAJaFH8UrSr87DJrFRHurRI4T7q1yzkgU8XrTHBwZDZD',
    'app_secret' => 'e4647b87a6b18da6803bddc3b3349674', 
    'verification'=>'d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852',
  ],
  'facebook'=> [
    'token' => 'EAAGrT16HtJgBABfLPgpcl6b75v9NYAjZA3o1na8WetM3fH1u7yfgKzvJx7mZA7A3z6wZAghEXqJuwAvXo3ZCZCpjcR4ZAZBSKlpmXWVfuIFpwdVZCjNy605M3BN3zxCUyCauhqlZC8PjF5RQ1jHAel1bNR1Gu6k60AEZAA1wpXAkMpm2F42IX6QiFn',
    'app_secret' => 'e4647b87a6b18da6803bddc3b3349674', 
    'verification'=>'d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852',
  ]
];

$doctrineCacheDriver = new FilesystemCache(__DIR__);
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

//INICIO DE LA CONVERSACIÓN

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