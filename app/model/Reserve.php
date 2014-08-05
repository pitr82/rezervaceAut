<?php

/**
 * Description of Reserve
 *
 * @author Petr Stefan
 */
namespace App\Model;

use Nette;

class Reserve extends Base{
    
    private $mailer;
    
    public function __construct(\Nette\Database\Context $db, Nette\Mail\IMailer $mailer) {
	$this->mailer = $mailer;
	parent::__construct($db);
    }
    
    /**
     *
     * @param int $rozsah	--rozsah zobrazení def. 1 [dny] 
     * @param int $interval	--interval pro rezervace def. 30 [minuty]
     * @return string		--tabulku s rezervacemi
     */
    public function denniRezervace($den) 
    {
	$dotaz = $this->database->table('rezervace')
		->where("rezervaceOd > ? AND rezervaceOd < ?", $den.' 00:00:00', $den.' 23:59:59')
		->where("rezervaceDo > ? AND rezervaceDo < ?", $den.' 00:00:00', $den.' 23:59:59')
		->order('auto_id, rezervaceOd ASC');
	return $dotaz;
    }
    
    /**
     * Funkce vložení SQL dotazu do tabulky rezervace
     * 
     * @param string $den	den rezervace [2014-05-14]
     * @param string $casOd	čas začátku rezervace [11:00:00]
     * @param string $casDo	čas konce rezervace [11:30:00]
     * @param int $userId	id uživatele
     */
    public function pridejRezervaci($den, $casOd, $casDo, $auto, $userId, $spravceID, $destinace, $jinyRidic = null)
    {
	$od = $den.' '.$casOd;
	$do = $den.' '.$casDo;
	$sql = $this->database->table('rezervace')
		->where("rezervaceOd >= ? AND rezervaceOd <= ?", $den.' 00:00:00', $den.' 23:59:59') //omezeni pro aktualní den 
		->where("(rezervaceOd >= ? AND rezervaceDo <= ?) OR " //rezervace obsahuje interval
			. "(rezervaceOd <= ? AND rezervaceDo >= ?) OR "	//rezervace je uvnitr intervalu
			. "(rezervaceOd > ? AND rezervaceOd < ?) OR " //rezervace za začíná mimo interval a končí v intervalu
			. "(rezervaceDo > ? AND rezervaceDo < ?)" //rezervace začíná v intervalu a končí mimo interval
			. "",$od ,$do ,$od ,$do, $od ,$do, $od ,$do)
		->where("auto_id = ?",$auto);
	if(!count($sql)){
	    $this->database->table('rezervace')->insert(array(
		'auto_id' => $auto,
		'zamestnanec_id' => $userId,
		'rezervaceOd' => $den.' '.$casOd,
		'rezervaceDo' => $den.' '.$casDo,
		'spravce_id' =>	$spravceID,
		'destinace' => $destinace,
		'jinyRidic' => $jinyRidic
	    ));
	    return true;    
	}else{
	    return false;
	}
    }
    
    /** Při smazání rezervace odešle email následující rezervaci
     * 
     * @param mixed $rezervace			rezervace pro smazání
     * @param mixed $nasledujiciRezervace	následující rezervace
     * @param string $basePath			basePath aplikace
     */
    public function sendEmailDeleteReservation($rezervace, $nasledujiciRezervace, $basePath) {
	$fileLatte = dirname(dirname(__FILE__)).'/templates/Scripts/reserveDelMailNtf.latte';
	$template = new Nette\Templating\FileTemplate($fileLatte);
	$template->registerFilter(new Nette\Latte\Engine);
	$template->registerHelperLoader('Nette\Templating\Helpers::loader');
	$template->basePath = $basePath;
	$template->jmeno = $rezervace->zamestnanec->jmeno." ".$rezervace->zamestnanec->prijmeni;
	$template->od = $rezervace->rezervaceOd;
	$template->do = $rezervace->rezervaceDo;
	$template->email = $rezervace->zamestnanec->email;
	$template->tel = $rezervace->zamestnanec->tel;
	$template->id = $rezervace->id;
	$template->autoN = $nasledujiciRezervace->auto->znackaAuta->znacka;
	$template->spzN = $nasledujiciRezervace->auto->spz;
	$template->odN = $nasledujiciRezervace->rezervaceOd;
	$template->doN = $nasledujiciRezervace->rezervaceDo;
	$template->vytvorenoN = $nasledujiciRezervace->vytvoreno;
	$template->destinaceN = $nasledujiciRezervace->destinace;
	$template->jmenoN = $nasledujiciRezervace->zamestnanec->jmeno." ".$nasledujiciRezervace->zamestnanec->prijmeni;
	$template->emailN = $nasledujiciRezervace->zamestnanec->email;
	$template->telN = $nasledujiciRezervace->zamestnanec->tel;
	$template->idN = $nasledujiciRezervace->id;

	$mail = new Nette\Mail\Message;
	$mail->setFrom('Systém rezervace aut <no-reply-is-rezervace@vitkovice.cz>')
		->addTo($nasledujiciRezervace->zamestnanec->email)
		->setSubject('Změna v rezervaci před Vámi')
		->setHtmlBody($template);
	$this->mailer->send($mail);
    }
    /** Při smazání rezervace odešle email následující rezervaci
     * 
     * @param mixed $rezervace			rezervace pro smazání
     * @param mixed $nasledujiciRezervace	následující rezervace
     * @param int   $delUser			jméno uživatle, který smazal rezervaci
     */
    public function sendEmailDeleteReservationAdmin($rezervace, $delUserName, $delUserEmail) {
	$fileLatte = dirname(dirname(__FILE__)).'/templates/Scripts/reserveDelAdminMailNtf.latte';
	$template = new Nette\Templating\FileTemplate($fileLatte);
	$template->registerFilter(new Nette\Latte\Engine);
	$template->registerHelperLoader('Nette\Templating\Helpers::loader');
	$template->jmeno = $rezervace->zamestnanec->jmeno." ".$rezervace->zamestnanec->prijmeni;
	$template->od = $rezervace->rezervaceOd;
	$template->do = $rezervace->rezervaceDo;
	$template->email = $rezervace->zamestnanec->email;
	$template->tel = $rezervace->zamestnanec->tel;
	$template->id = $rezervace->id;
	$template->destinace = $rezervace->destinace;
	$template->vytvoreno = $rezervace->vytvoreno;
	$template->auto = $rezervace->auto->znackaAuta->znacka;
	$template->spz = $rezervace->auto->spz;
	$template->delUserName = $delUserName;
	$template->delUserEmail = $delUserEmail;

	$mail = new Nette\Mail\Message;
	$mail->setFrom('Systém rezervace aut <no-reply-is-rezervace@vitkovice.cz>')
		->addTo($rezervace->zamestnanec->email)
		->setSubject('Odstranění rezervace')
		->setHtmlBody($template);
	$this->mailer->send($mail);
    }
    
    /**
     * Funkce editace SQL dotazu do tabulky rezervace
     * 
     * @param string $den	den rezervace [2014-05-14]
     * @param string $casOd	čas začátku rezervace [11:00:00]
     * @param string $casDo	čas konce rezervace [11:30:00]
     * @param int $auto		id automobilu
     * @param int $userId	id uživatele
     * @param int $idRezervace	id rezervace
     * @param string $basePath  BasePath aplikace
     */
    public function editujRezervaci($den, $casOd, $casDo, $auto, $userId, $idRezervace, $basePath, $destinace, $jinyRidic = null)
    {
	$od = $den.' '.$casOd;
	$do = $den.' '.$casDo;
	//zahajíme transakci
	//$this->database->beginTransaction();
	$sql = $this->database->table('rezervace')
		->where("rezervaceOd >= ? AND rezervaceOd <= ?", $den.' 00:00:00', $den.' 23:59:59') //omezeni pro aktualní den 
		->where("(rezervaceOd >= ? AND rezervaceDo <= ?) OR " //rezervace obsahuje interval
			. "(rezervaceOd <= ? AND rezervaceDo >= ?) OR "	//rezervace je uvnitr intervalu
			. "(rezervaceOd > ? AND rezervaceOd < ?) OR " //rezervace za začíná mimo interval a končí v intervalu
			. "(rezervaceDo > ? AND rezervaceDo < ?)" //rezervace začíná v intervalu a končí mimo interval
			. "",$od ,$do ,$od ,$do, $od ,$do, $od ,$do)
		->where("auto_id = ?",$auto)
		->where("id != ?", $idRezervace);
	if(!count($sql)){
	    $rezervace = $this->database->table('rezervace')->get($idRezervace);
	    //kontrola na zkrácení rezervace 
	    if(($rezervace->auto_id == $auto) && ($rezervace->rezervaceOd == $od) && 
		    (strtotime($rezervace->rezervaceDo) > strtotime($do))  )
	    {
		$nasledujiciRezervace = $this->nasledujiciRezervace($rezervace->auto_id, $rezervace->rezervaceDo, $den.' 23:59:00');
		// pošli email, jen pokud je další rezervace a pokud je další rezervace do 2 hodin 
		if ($nasledujiciRezervace && (strtotime($nasledujiciRezervace->rezervaceOd) - strtotime($do)) <= 7200 ){
		    // --> odešle dalšímu informační email, že se rezervace před ním již uvolnila
		    $fileLatte = dirname(dirname(__FILE__)).'/templates/Scripts/reserveChngMailNtf.latte';
		    $template = new Nette\Templating\FileTemplate($fileLatte);
		    $template->registerFilter(new Nette\Latte\Engine);
		    $template->registerHelperLoader('Nette\Templating\Helpers::loader');
		    $template->odP = $od;
		    $template->doP = $do;
		    $template->basePath = $basePath;
		    $template->jmeno = $rezervace->zamestnanec->jmeno." ".$rezervace->zamestnanec->prijmeni;
		    $template->od = $rezervace->rezervaceOd;
		    $template->do = $rezervace->rezervaceDo;
		    $template->email = $rezervace->zamestnanec->email;
		    $template->tel = $rezervace->zamestnanec->tel;
		    $template->id = $rezervace->id;
		    $template->autoN = $nasledujiciRezervace->auto->znackaAuta->znacka;
		    $template->spzN = $nasledujiciRezervace->auto->spz;
		    $template->odN = $nasledujiciRezervace->rezervaceOd;
		    $template->doN = $nasledujiciRezervace->rezervaceDo;
		    $template->vytvorenoN = $nasledujiciRezervace->vytvoreno;
		    $template->destinaceN = $nasledujiciRezervace->destinace;
		    $template->jmenoN = $nasledujiciRezervace->zamestnanec->jmeno." ".$nasledujiciRezervace->zamestnanec->prijmeni;
		    $template->emailN = $nasledujiciRezervace->zamestnanec->email;
		    $template->telN = $nasledujiciRezervace->zamestnanec->tel;
		    $template->idN = $nasledujiciRezervace->id;

		    $mail = new Nette\Mail\Message;
		    $mail->setFrom('Systém rezervace aut <no-reply-is-rezervace@vitkovice.cz>')
			    ->addTo($nasledujiciRezervace->zamestnanec->email)
			    ->setSubject('Vyzvednutí auta')
			    ->setHtmlBody($template);
		    $this->mailer->send($mail);
		}
	    }	
	    /* pro update si zvolíme novou proměnnou, abychom si nesmazali půvorní data pro odeslání emailu */
	    $updateRezervace = $this->database->table('rezervace')->get($idRezervace);
	    $updateRezervace->update(array(
		    'auto_id' => $auto,
		    'spravce_id' => $userId,
		    'rezervaceOd' => $den.' '.$casOd,
		    'rezervaceDo' => $den.' '.$casDo, 
		    'destinace' => $destinace,
		    'jinyRidic' => $jinyRidic
		));	    
	  // pokud rezervaci upravil jiný uživatel než vlastník, pošli mail
	    if($rezervace->zamestnanec_id != $userId){
		$fileLatte = dirname(dirname(__FILE__)).'/templates/Scripts/reserveChngAdminMailNtf.latte';
	        $template = new Nette\Templating\FileTemplate($fileLatte);
		$template->registerFilter(new Nette\Latte\Engine);
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		$template->basePath = $basePath;
		$template->editUserName = $rezervace->ref('zamestnanec', 'spravce_id')->jmeno." ".$rezervace->ref('zamestnanec', 'spravce_id')->prijmeni;
		$template->editUserEmail = $rezervace->ref('zamestnanec', 'spravce_id')->email;
		$template->id = $rezervace->id;
		$template->odP = $rezervace->rezervaceOd;
		$template->doP = $rezervace->rezervaceDo;
		$template->autoP = $rezervace->auto->znackaAuta->znacka;
		$template->spzP = $rezervace->auto->spz;
		$template->vytvorenoP = $rezervace->vytvoreno;
		$template->destinaceP = $rezervace->destinace;
		$template->jinyRidicP = $rezervace->jinyRidic;
		/* aktualizujeme údaje*/
		$template->odN = $updateRezervace->rezervaceOd;
		$template->doN = $updateRezervace->rezervaceDo;
		$template->autoN = $updateRezervace->auto->znackaAuta->znacka;
		$template->spzN = $updateRezervace->auto->spz;
		$template->vytvorenoN = $updateRezervace->vytvoreno;
		$template->destinaceN = $updateRezervace->destinace;
		$template->jinyRidicN = $updateRezervace->jinyRidic;
		
		$mail = new Nette\Mail\Message;
		$mail->setFrom('Systém rezervace aut <no-reply-is-rezervace@vitkovice.cz>')
			    ->addTo($rezervace->zamestnanec->email)
			    ->setSubject('Změna v rezervaci auta !')
			    ->setHtmlBody($template);
		$this->mailer->send($mail);
	    }
	    
	    return true;    
	}else{
	    return false;
	}
    }
    /**Aktivní rezervace pro aktualního zaměstnance
     * 
     * @param string $cas   Datetime aktuálního času [Y-m-d H:i:s]
     * @param int	    ID zaměstnance
     * @return mixed	    Objekt aktivnich rezervaci
     */
    public function aktivniRezervace($cas, $zamestnanec)
    {
	return $this->database->table('rezervace')
		->where("rezervaceOd >= ? AND zamestnanec_id = ?", $cas, $zamestnanec)->order('rezervaceOd ASC');
    }
    
    /**Aktuální rezervace pro admin
     * 
     * @param string $cas   Datetime aktuálního času [Y-m-d H:i:s]
     * @return mixed	    Objekt aktivnich rezervaci
     */
    public function aktualniRezervace($cas)
    {
	return $this->database->table('rezervace')
		->where("rezervaceDo >= ?", $cas)->order('rezervaceOd ASC');
    }
    /**
     * 
     * @param string $casDo	konečný čas rezervace
     * @return mixed		následující nejbližší rezervace
     * 
     */
    public function nasledujiciRezervace($auto, $casDo, $maxCasDo) {
	return $this->database->table('rezervace')
		->where('rezervaceOd >= ?', $casDo)
		->where('auto_id = ?', $auto)
		->where('rezervaceDo <= ?', $maxCasDo)
		->limit(1)->fetch();
    }
    
    /**
     * Odstraní rezervecí podle id
     * @param int $idRezervace
     * @return mixed		Objekt rezervace
     */
    public function smazRezervaci($idRezervace)
    {
	return $this->database->table('rezervace')->where('id',$idRezervace)->delete();
    }
    /**
     * Vratí rezervaci podle id
     * @param int $idRezervace
     * @return mixed		Objekt rezervace
     */
    public function vratRezervaci($idRezervace)
    {
	return $this->database->table('rezervace')->get($idRezervace);
    }
    
    public function blizkeRezervace($utvar = NULL, $limit = 10){
	if($utvar)
	    return $this->database->table('rezervace')->where("rezervaceDo >= ?", date('Y-m-d H:i:s'))->where('zamestnanec.utvar_id = ?',$utvar)->order('rezervaceOd');
	else
	    return $this->database->table('rezervace')->where("rezervaceDo >= ?", date('Y-m-d H:i:s'))->order('rezervaceOd');
    }
    
    public function vratJmenaZamestnancu() 
    {
	   return  $this->database->table('zamestnanec')->select('prijmeni, jmeno')->order('prijmeni ASC');
    }
    
}
