<?php
namespace App\Model;

use Nette,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Description of Scripts
 *
 * @author Petr Stefan
 */
class Scripts extends \Nette\Object
{
    
      /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
	    $this->database = $database;
    }
    
    
    public function checkRezervationForNotification($basePath) 
    {
	$reservations =  $this->database->table('rezervace')
		->where('potvrzeno IS NULL')
		->where('rezervaceOd >= ?', $this->database->literal('NOW()'))
		->where('rezervaceOd <= ?', $this->database->literal('DATE_ADD(NOW(), INTERVAL '.\Constants::NOTIFICATION_INTERVAL.')'));
	/* Odeslání pomécí mail na kerio2.vitkovice.cz - pomalé */
//	$mailer = new SendmailMailer();
	/* Odeslání na chronos v lokální sítí - rychlé*/
	$mailer = new Nette\Mail\SmtpMailer(array(
	    'host' => '192.168.3.2',
	));
		
	foreach ($reservations as $res){
//	    try {
		$email = $res->zamestnanec->email;
		$fileLatte = dirname(dirname(__FILE__)).'/templates/Scripts/reserveMailNtf.latte';
		$template = new Nette\Templating\FileTemplate($fileLatte);
		$template->registerFilter(new Nette\Latte\Engine);
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		$template->basePath = $basePath;
		$template->auto = $res->auto->znackaAuta->znacka;
		$template->spz = $res->auto->spz;
		$template->od = $res->rezervaceOd;
		$template->do = $res->rezervaceDo;
		$template->vytvoreno = $res->vytvoreno;
		$template->jmeno = $res->zamestnanec->jmeno." ".$res->zamestnanec->prijmeni;
		$template->email = $res->zamestnanec->email;
		$template->tel = $res->zamestnanec->tel;
		$template->id = $res->id;
		
		$mail = new Message;
		$mail->setFrom('Systém rezervace aut <no-reply-is-rezervace@vitkovice.cz>')
			->addTo($res->zamestnanec->email)
			->setSubject('Vyzvednutí auta')
			->setHtmlBody($template);
		
		
		
		
		$mailer->send($mail);
		$this->database->table('rezervace')->get($res->id)->update(array('potvrzeno' => $this->database->literal('NOW()')));
		
		/* Odchytávání chyby odeslání emailu */
//	    } catch (Nette\InvalidStateException $e){
//		$pom = 2;
//	    }
	}
    }
}
