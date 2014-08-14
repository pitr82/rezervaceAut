<?php
namespace App\Presenters;

use App\Components,
	App\Model,
	Nette,
	App\Config\Constants,
	Nette\Diagnostics\Debugger,
	Nette\Application\UI\Form;

/**
 * Description of ReservePresenter
 *
 * @author Petr Stefan
 */
class ReservePresenter extends SecurePresenter
{
    /** 
     * @inject
     * @var Model\DatumCas
     */
    public $DatumCas;

    /** 
     * @inject
     * @var Model\Reserve 
     */
    public $Reserve;
    
    /** 
     * @inject
     * @var Model\Unit 
     */
    public $Unit;
        
    /** @persistent string*/
    public $datum;
    
    /*třídní proměnné*/
    private $pocetDnuNaRezervaci;
    
       
    public function startup()
    {
	parent::startup();
	
	// vytvoříme pole pro rezervace
	$this->pocetDnuNaRezervaci = $this->DatumCas->rezervaceNaDny(\Constants::DNY_NA_REZERVACI);
	//validace proti podvrhnutí přímo z Url
	$tmpDate = $this->getParameter('datum', date('Y-m-d'));
	$pom = explode('-', $tmpDate);
	if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $tmpDate) && checkdate($pom[1], $pom[2], $pom[0])){
	    $this->datum = $tmpDate;
	}
	else{
	    $this->datum = date('Y-m-d');
	}
	
	/* Rezervace na celé dny jsou dostupné jen pro administratora v default akci jinde se nezobrazují, tudíž je odstraníme */
	if($this->user->identity->role == 'admin' && $this->action != 'default'){
	$form = $this['formReserve'];
	$form->removeComponent($form['allDays']);
	$form->removeComponent($form['dny']);
	}

    }

    public function actionEdit($datum, $id)
    {
	// Kontrola vlastníka rezervace
	if ($this->user->identity->role == 'admin'){
	   $rezervace = $this->Reserve->aktualniRezervace(date('Y-m-d H:i:s'));
	}else{
	    $rezervace = $this->Reserve->aktivniRezervace(date('Y-m-d H:i:s'), $this->user->getId());
	}
	if(!$rezervace->get($id)){
	    $this->flashMessage('Vámi zvolená rezervace naní platná.','error');
	    $this->redirect('Reserve:list');
	}
	
	$reservace = $this->Reserve->vratRezervaci($id);
	$casOd = explode(' ',$reservace->rezervaceOd);
	$casDo = explode(' ',$reservace->rezervaceDo);

	$this['zvolDatum']->setDefaults(array('datum' => $casOd[0]));
	$this['formReserve']->setDefaults(array(
	    'den' => $casOd[0], 
	    'casOd' => $casOd[1], 
	    'casDo' => $casDo[1], 
	    'auto_id' => $reservace->auto, 
	    'destinace' => $reservace->destinace, 
	    'jinyRidic' => $reservace->jinyRidic
	));
    }
    
    public function handleDelete($id)
    {
	/*Aktivní rezervace uživatele*/
	if ($this->user->identity->role == 'admin'){
	   $rezervace = $this->Reserve->aktualniRezervace(date('Y-m-d H:i:s'));
	}else{
	    $rezervace = $this->Reserve->aktivniRezervace(date('Y-m-d H:i:s'), $this->user->getId());
	}
	$rezervace = $rezervace->select('*')->where('id', $id)->fetch();
	
	if( $rezervace && $this->Reserve->smazRezervaci($id)){
//	    $datum = date('Y-m-d', strtotime($rezervace->rezervaceDo));
	    $datum = new \DateTime($rezervace->rezervaceOd);
	    $nasledujiciRezervace = $this->Reserve->nasledujiciRezervace($rezervace->auto_id, $rezervace->rezervaceDo, $datum->format('Y-m-d').' 23:59:00');
	    // pošli email, jen pokud je další rezervace a pokud je další rezervace do 2 hodin  
	    if ( $nasledujiciRezervace && (strtotime($nasledujiciRezervace->rezervaceOd) - strtotime($rezervace->rezervaceDo)) <= 7200){
		$host = $this->context->httpRequest->url->hostUrl;
		$path = $this->context->httpRequest->url->basePath;
		$this->Reserve->sendEmailDeleteReservation($rezervace, $nasledujiciRezervace, $host.$path);
	    }
	    // pošli upozornění, že rezervace byla zrušena jiným uživatelem.
	    if($this->user->getId() != $rezervace->zamestnanec_id){
		$this->Reserve->sendEmailDeleteReservationAdmin($rezervace, $this->user->identity->jmeno.' '.$this->user->identity->prijmeni, $this->user->identity->email);
	    }
	    
	    $this->flashMessage('Vaše rezervace byla úspěšně odstraněna', 'success');
	}else{
	    $this->flashMessage('Při odstranění rezervace došlo k chybě, rezervace nebyla odstraněna', 'error');
	}
	$this->redirect('this');
    }
    
    public function handleFormRef($value)
    {
	$this->datum = $value;
//	barDump($value);
	if($this->isAjax()){
	    $this->redrawControl('nadpis');
	    $this->redrawControl('tabRezervaci');
	}
    }
    
    public function handlePageUpdate($page)
    {
	    if($this->isAjax()){
		$this->getPaginator();
		$this->redrawControl('blizkerezervace');
		$this->redrawControl('reserveFormArea');
	    }
    }	
    
   
/* 
 * Používá přímy přístup do databáze, 
 * obslužný javascript v ReserveForm.latte
 * 
    public function handleAutoComplete($text) 
    {
	$this->payload->autoComplete = array();
	$text = trim($text);
	if ($text !== '') {
	    
// vytvoříme seznam pro našeptávač
	    foreach ($this->Reserve->vratJmenaZamestnancu() as $zamestnanec) {
		$item = trim($zamestnanec->prijmeni.' '.$zamestnanec->jmeno);
		if (strncasecmp($item, $text, strlen($text)) === 0) {
		    $this->payload->autoComplete[] = $item;
		}
	    }
	}
// činnost presenteru tímto můžeme ukončit
	$this->terminate();
    }
*/
private function getPaginator()
    {
	/* strankování  */
        if ($this->user->identity->role == 'admin'){
	    $sql = $this->Reserve->blizkeRezervace();
	}else{
	    $sql = $this->Reserve->blizkeRezervace($this->user->identity->utvar_id);
	}
	$paginator = new Nette\Utils\Paginator;
	$paginator->setItemCount($sql->count('*'));
	$paginator->setItemsPerPage(12); 
	if(!$this->getParameter('page'))
	    $paginator->page = 1;
	else
	    $paginator->page = $this->getParameter('page');
	$this->template->paginator = $paginator;
	$this->template->blizkeRezervace = $sql->limit($paginator->getLength(), $paginator->getOffset());
    }

        public function renderDefault($datum)
    {
	$this->template->dny = $this->pocetDnuNaRezervaci;
	$this->template->datum = $this->datum;
	if ($this->user->identity->role == 'admin'){
	    $this->template->utvary = $this->Unit->vypisUtvary();
	}else{
	    $this->template->autaUtvaru = $this->Unit->autaUtvaru($this->user->identity->utvar_id);
	}
	if(!isset($this->template->paginator)){
	    $this->getPaginator();
	}
	foreach ($this->Reserve->vratJmenaZamestnancu() as $zamestnanec) {
		$uzivatele[] = trim($zamestnanec->prijmeni.' '.$zamestnanec->jmeno);
	}
	$this->template->uzivatele = $uzivatele; 
	
//	barDump($this->session->getId());
    }
    
    public function renderEdit($id)
    {
	$this->template->datum = $this->getParameter('datum');
	$this->template->dny = $this->pocetDnuNaRezervaci;
	$this->template->blizkeRezervace = $this->Reserve->blizkeRezervace();
	if ($this->user->identity->role == 'admin'){
	    $this->template->utvary = $this->Unit->vypisUtvary();
	    $this->template->disableMultiReserve = true;
	}else{
	    $this->template->autaUtvaru = $this->Unit->autaUtvaru($this->user->identity->utvar_id);
	}
	
	if(!isset($this->template->paginator)){
	    $this->getPaginator();
	}
	foreach ($this->Reserve->vratJmenaZamestnancu() as $zamestnanec) {
		$uzivatele[] = trim($zamestnanec->prijmeni.' '.$zamestnanec->jmeno);
	}
	$this->template->uzivatele = $uzivatele; 
    }
    
    public function renderList($datum)
    {
	$this->template->datum = $this->datum;
	$this->template->aktivniRezervace = $this->Reserve->aktivniRezervace(date('Y-m-d H:i:s'), $this->user->getId());
	/* pokud je uživatel admin, zobraz aktivní a naplanované rezervace */
	if ($this->user->identity->role == 'admin'){
	    $this->template->aktualniRezervace = $this->Reserve->aktualniRezervace(date('Y-m-d H:i:s'));
	}else{
	    $this->template->autaUtvaru = $this->Unit->autaUtvaru($this->user->identity->utvar_id);
	}
	
    }

    protected function createComponentTabRezervaci()
    {
	if ($this->user->identity->role == 'admin'){
	    $trezervace = new Components\TabRezervaciControl($this->base, $this->datum, 'all');
	}else{
	    $trezervace = new Components\TabRezervaciControl($this->base,$this->datum, $this->user->identity->utvar_id);
	}
	return $trezervace;
    }
    
    protected function  createComponentZvolDatum()
    {
	$form = new Form;
	$form->addText('datum', 'Datum Rezervace')
		->setDefaultValue($this->datum)
		->addRule(Form::PATTERN,'Správné datum je ve formátu : '.date('Y-m-d'),'([0-9]\s*){4}-([0-9]\s*){2}-([0-9]\s*){2}');
	$form->addSubmit('sendDatum', 'Zobrazit rezervace');
	$form->onSuccess[] = $this->zvolDatumSucceeded;
	return $form;
    }
    public function zvolDatumSucceeded($form)
    {
	$values = $form->getValues();
	$this->datum = $values->datum;
	$this->redirect('this', $this->datum);

    }

        protected function createComponentFormReserve() 
    {
	$form = new Form;
	//proměnné pro form
	$casy = $this->DatumCas->rozsahHodin(true);
	$dny = $this->DatumCas->rezervaceNaCeleDny(\Constants::DNY_NA_REZERVACI);
	/* utvar nemusí mít žádné auto */
	$poleAut = null;
	 if ($this->user->identity->role == 'admin'){
	    foreach($this->base->vypisAuta() as $auto){
		$poleAut[$auto->id] = $auto->spz.' '.$auto->znackaAuta->znacka;
	    }
	    $form->addCheckbox('allDays')
		->addCondition($form::EQUAL, TRUE)
		->toggle('rezMulti')
		->toggle('rezSingle', FALSE);
	    $form->addCheckboxList('dny', 'Zvolte dny',$dny)
		->setAttribute('id', 'dny')
		 ->setAttribute('class', 'ajax');
	   
	 }else{
	     foreach($this->base->vypisUtvarZamestnance($this->user->identity->utvar_id)->related('utvar_auto')->order('auto.znackaAuta.znacka') as $utvar_auto){
		
		$poleAut[$utvar_auto->auto->id] = $utvar_auto->auto->spz.' '.$utvar_auto->auto->znackaAuta->znacka;
	    }
	 }
	if($poleAut != null){
	$form->addRadioList('auto_id', 'Auta',$poleAut)
		->setRequired('Nevybrali jste si žádné auto.');
	}
	$form->addRadioList('den', 'Rezervace', $this->pocetDnuNaRezervaci)
		->setDefaultValue(date('Y-m-d'))
		->setAttribute('class', 'ajax');
	
	$form->addSelect('casOd', 'Hodiny od',$casy)
		->setDefaultValue($this->DatumCas->defaultHodinyOd())
		->setAttribute('id', 'casOd');
	$form->addSelect('casDo', 'do', $casy)
		->setDefaultValue($this->DatumCas->defaultHodinyDo())
		->setAttribute('id', 'casDo');
	$form->addText('destinace', 'Cíl cesty')
		->setRequired('Zvolte cíl cesty')
		->setAttribute('size', 60);
	$form->addText('jinyRidic', 'Zvolte jiného řidiče')
		->setAttribute('id','jinyRidic')
		->setAttribute('size', 40);
	
	$form->addSubmit('send', 'Rezervovat auto');
	$form->onSuccess[] = $this->formReserveSucceeded;
	
	//$form->getElementPrototype()->class("ajax");

	
	return $form;
    }
    
    public function formReserveSucceeded($form)
    {
//	Debugger::fireLog('Zpracování hodnot'); 
	$values = $form->getValues();
	$id = $this->getParameter('id');
	
// administrátor může upravovat již probíhající rezervaci
	
	if ($this->user->identity->role != 'admin'){
//kontrola správnosti rezervace času, čas Od je = nebo > než čas Do nebo čas Od musí být větší než aktuální čas
	    if((strtotime($values->casOd)>=strtotime($values->casDo) || strtotime($values->den.' '.$values->casOd) <= strtotime('now'))){
		$this->flashMessage('Rezervace neproběhla. Nemůžete se rezervovat na již neaktuální časový úsek.', 'error');
		$form->render($form['casOd']->getControlPrototype()->addClass('error'));
		$form->render($form['casDo']->getControlPrototype()->addClass('error'));
		return;
	    }
	}
	if(!$id){
	    // přidávání do databáze
	    if (($this->user->identity->role == 'admin') && ($values->allDays)){
		foreach($values->dny as $den){
		    $date = new \DateTime($den);
		    if ($this->Reserve->pridejRezervaci($den, '00:00:00', '23:59:00', $values->auto_id, $this->user->getId(), $this->user->getId(), $values->destinace, $values->jinyRidic )){
			$this->flashMessage('Přidání rezervace pro den '.$date->format('d.m. Y').' proběho úspěšně.', 'success');
		    }else{
			$this->flashMessage("Ve dne: ".$date->format('d.m. Y')." je auto již rezervováno.", 'error');
		    }
		}
		$this->redirect('list');
	    }else{
		if ($this->Reserve->pridejRezervaci($values->den, $values->casOd, $values->casDo, $values->auto_id, $this->user->getId(), $this->user->getId(), $values->destinace, $values->jinyRidic )){
		    $this->flashMessage('Přidání rezervace proběho úspěšně.', 'success');
		    $this->redirect('list',$datum = $values->den );
		}else{
		    $this->flashMessage('Rezervace neproběhla. Vámi zvolené auto není v tomto časovém období dostupné.', 'error');
		    $form->render($form['casOd']->getControlPrototype()->addClass('error'));
		    $form->render($form['casDo']->getControlPrototype()->addClass('error'));
		    $form->render($form['den']->getLabelPrototype()->addClass('error'));
		    $form->render($form['auto_id']->getLabelPrototype()->addClass('error'));
		    $form->render($form['destinace']->getLabelPrototype()->addClass('error'));
		}
	    }
	}else{
	    //editace - update v databázi
	    $host = $this->context->httpRequest->url->hostUrl;
	    $path = $this->context->httpRequest->url->basePath;
	    if ($this->Reserve->editujRezervaci($values->den, $values->casOd, $values->casDo, $values->auto_id, $this->user->getId(), $id, $host.$path, $values->destinace, $values->jinyRidic)){
		$this->flashMessage('Úspěšně jste upravili rezervaci.', 'success');
		$this->redirect('list',$datum = $values->den );
	    }else{
		$this->flashMessage('Úprava Rezervace neproběhla. Vámi zvolené auto není v tomto časovém období dostupné.', 'error');
		$form->render($form['casOd']->getControlPrototype()->addClass('error'));
		$form->render($form['casDo']->getControlPrototype()->addClass('error'));
		$form->render($form['den']->getLabelPrototype()->addClass('error'));
		$form->render($form['auto_id']->getLabelPrototype()->addClass('error'));
		$form->render($form['destinace']->getLabelPrototype()->addClass('error'));
	    }
	}
    }
    
    
    
    
    
   
    
    
}
