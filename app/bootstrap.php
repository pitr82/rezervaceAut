<?php
require __DIR__ . '/../vendor/autoload.php';
/* vlastní skript pro dumpování proměnných do status baru  */
require __DIR__ . '/../vendor/others/myLibs/barDump.php';




$configurator = new Nette\Configurator;

//$configurator->setDebugMode(FALSE);		// degugger disable -> Production server mode
//$configurator->setDebugMode(TRUE);		// debug mode MUST NOT be enabled on production server
//$configurator->setDebugMode('192.168.3.200'); // povolí debug pro adresy, více adres odděleno čárkou



$configurator->enableDebugger(__DIR__ . '/../log', 'petr.stefan@vitkovice.cz');

// volání vlastního maileru pro logování do emailu přenastavení Loggeru
require __DIR__ . '/../vendor/others/myLibs/MyLogger.php';
/* znovu musíme načíst parametry logDirectory, mail, callback pro mailer, fromHost */
Tracy\Debugger::setLogger(new myLibs\MyLogger(__DIR__ . '/../log', 'petr.stefan@vitkovice.cz',array('myLibs\MyLogger', 'myDefaultMailer'), 'vitkovice.cz'));
// volání vlastního maileru pro logování do emailu přenastavení maileru - DEPRECATED
//require __DIR__ . '/../vendor/others/myLibs/MyErrorMail.php';
//Tracy\Debugger::$mailer = array('\myLibs\MyErrorMail','myDefaultMailer');
//myLibs\MyErrorMail::$host = 'vitkovice.cz';


//$configurator->enableDebugger(__DIR__ . '/../log','pitr82@gmail.com'); //odešle chyby na email

/* při zakomentování nebude systém kešovat */
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../vendor/others')
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

/* funkce voláná při ukončení aplikace*/
//$container->getService('application')->onShutdown[] = function() use ($container){
//    $container->getByType('App\Model\Auto')->insCar();
//};

/* funkce voláná při spuštění aplikace*/
//$container->getService('application')->onStartup[] = function() {
//    Nette\Diagnostics\Debugger::fireLog('startup');    
//};


return $container;
