<?php

namespace App\Presenters;

use Nette,
    App\Model,
    myLibs\VisualPaginator,
    App\MyExceptions,
    Nette\Application\UI\Form;

/**
 * Description of SecurePresenter
 *
 * @author Petr Stefan
 * 
 */
class AutoPresenter extends SecurePresenter 
{
    /**
     * @inject
     * @var Model\Auto
     */
    public $auto;
    
    
   public function handleEditAuto($id)
    {
	if($utvar = $this->auto->vratAuto($id))
	    $this['formAuto']->setDefaults($utvar);
	if($this->isAjax())
	    $this->redrawControl('editAuto');
    }
    
    
    public function handleDeleteAuto($id)
    {
	try {
	    
	    if($this->auto->smazAuto($id)){
		$this->flashMessage('Auto bylo úspěšně odstraněno.', 'success');
	    }else{
		$this->flashMessage('Auto nemohlo být odstraněno.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Auto nemohlo byt odstraněno: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }
   
    
    public function handleEditZnacka($id)
    {
	if($znacka = $this->auto->vratZnacka($id))
	    $this['formZnacka']->setDefaults($znacka);
	if($this->isAjax())
	    $this->redrawControl('editZnacka');
    }
    
    
    public function handleDeleteZnacka($id)
    {
	try {
	    
	    if($this->auto->smazZnacka($id)){
		$this->flashMessage('Značka auta byla úspěšně odstraněna.', 'success');
	    }else{
		$this->flashMessage('Značka auta nemohla být odstraněna.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Značka nemohla byt odstraněna: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }
    
    
    public function handleEditTyp($id)
    {
	if($vjezdy = $this->auto->vratTyp($id))
	    $this['formTyp']->setDefaults($vjezdy);
	if($this->isAjax())
	    $this->redrawControl('editType');
    }
    
    
    public function handleDeleteTyp($id)
    {
	try {
	    
	    if($this->auto->smazTyp($id)){
		$this->flashMessage('Typ auta byla úspěšně odstraněna.', 'success');
	    }else{
		$this->flashMessage('Typ auta nemohla být odstraněna.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Typ auta nemohl byt odstraněn: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }
    
    public function handleEditExterniVjezd($id)
    {
	if($typ = $this->auto->vratExterniVjezd($id))
	    $this['formExterniVjezd']->setDefaults($typ);
	if($this->isAjax())
	    $this->redrawControl('editExterniVjezd');
    }
    
    public function handleDeleteExterniVjezd($id)
    {
	try {
	    
	    if($this->auto->smazExterniVjezd($id)){
		$this->flashMessage('Externi vjezd byl úspěšně odstraněn.', 'success');
	    }else{
		$this->flashMessage('Externi vjezd nemohl být odstraněn.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Externi vjezd nemohl byt odstraněn: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }
    

    public function handleAutoExtForm($value)
    {
        if ($value) {
            $this['autoExtForm']['ext_id']
                ->setItems($this->auto->vypisExterniVjezdy()->fetchPairs('id', 'nazev'))
		->setDefaultvalue($this->auto->externiVjezdAuta($value));
        } else {
            $this['autoExtForm']['ext_id']->setItems(array());
        }
        $this->redrawControl('tabExtCar');
    }
    
    
    public function renderList($id) {
        $this->template->auta = $this->auto->vypisAuta();
        $this->template->znacky = $this->auto->vypisZnacky();
        $this->template->typy = $this->auto->vypisTypy();
        $this->template->externiVjezdy = $this->auto->vypisExterniVjezdy();
    }
    
   
    
    public function renderDefault() {
	$this->redirect('Auto:list');
    }
   
    
    protected function createComponentFormAuto() {
	$form = new Form();
	$form->addSelect('firma_id', 'Firma', $this->auto->vypisFirmy()->fetchPairs('id', 'nazev'));
	$form->addSelect('typAuta_id', 'Typ', $this->auto->vypisTypy()->fetchPairs('id', 'typ'))
		->setDefaultValue('1');

	$form->addSelect('znackaAuta_id', 'Značka', $this->auto->vypisZnacky()->fetchPairs('id','znacka'));
	$form->addText('spz', 'Spz')
		->setAttribute('placeholder', 'SPZ - 1T1 1234')
		->setRequired('Zadejte SPZ auta.');
	$form->addText('popis', 'Popis')
		->setAttribute('placeholder', 'Podrobnější info')
		->setRequired('Zadejte Podrobnější popis auta.');
	
	$form->addSubmit('send', 'Přidej/Edituj');
	
	$form->onSuccess[] = $this->formAutoSucceeded;
	
	return $form;
    }
    
    public function formAutoSucceeded($form) {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->auto->addAuto($form->getValues());
		$this->flashMessage('Přidání auta proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Auto nemohlo byt přidáno. '.$e->getMessage(), 'error');
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->auto->editAuto($form->getValues(), $id);
		$this->flashMessage('Editace auta proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci auta nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    protected function createComponentFormZnacka() 
    {
	$form = new Form;
	$form->addText('znacka', 'Značka')
		->setRequired('Nezadali jste značku auta.')
		->setAttribute('placeholder', 'značku auta');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formZnackaSucceeded;
	return $form;
    }
    
    public function formZnackaSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->auto->addZnacka($form->getValues());
		$this->flashMessage('Přidání značky proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Značka nemohla byt přidána. '.$e->getMessage(), 'error');
		$form->render($form['znacka']->getControlPrototype()->addClass('error'));
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->auto->editZnacka($form->getValues(), $id);
		$this->flashMessage('Editace značky proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci značky nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    protected function createComponentFormTyp() 
    {
	$form = new Form;
//	$form->getElementPrototype()->class("ajax");
	$form->addText('typ', 'Typ')
		->setRequired('Nezadali jste typ auta.')
		->setAttribute('placeholder', 'typ auta');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formTypSucceeded;
	return $form;
    }
    
    public function formTypSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->auto->addtyp($form->getValues());
		$this->flashMessage('Přidání typu auta proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Typ auta nemohla byt přidána. '.$e->getMessage(), 'error');
		$form->render($form['typ']->getControlPrototype()->addClass('error'));
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->auto->editTyp($form->getValues(), $id);
		$this->flashMessage('Editace typu auta proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci typu auta nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    protected function createComponentFormExterniVjezd() 
    {
	$form = new Form;
	$form->addText('nazev', 'Název')
		->setRequired('Nezadali jste název pro externí vjezd.')
		->setAttribute('placeholder', 'Externí vjezd');
	$form->addText('zkratka', 'Zkratka')
		->setRequired('Nezadali jste zkratku pro externí vjezd.')
		->setAttribute('placeholder', 'Zkratka externího vjezdu');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formExterniVjezdSucceeded;
	return $form;
    }
    
    public function formExterniVjezdSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->auto->addExterniVjezd($form->getValues());
		$this->flashMessage('Přidání externího vjezdu proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Externí vjezd nemohl byt přidán. '.$e->getMessage(), 'error');
		$form->render($form['nazev']->getControlPrototype()->addClass('error'));
		$form->render($form['zkratka']->getControlPrototype()->addClass('error'));
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->auto->editExterniVjezd($form->getValues(), $id);
		$this->flashMessage('Editace externího vjezdu proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci externího vjezdu nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    
    protected function createComponentAutoExtForm()
    {
	$form = new Form;
	$auta = $this->auto->vypisAuta();
	$vjezdyId = $this->auto->vypisExterniVjezdy()->fetchPairs('id', 'nazev');
	
	foreach ($auta as $auto) {
		$form->addCheckboxList($auto->id, $auto->spz, $vjezdyId)
		    ->setDefaultValue($auto->related('auto_externiVjezd.auto_id')->fetchPairs(NULL, 'externiVjezd_id'));
	}
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->autoExtFormSucceeded;
	return $form;
    }
    
    public function autoExtFormSucceeded(Form $form)
    {
        // $form->getValues() ignores invalidated input's values
        $values = $form->getHttpData();
        unset($values['send']);
	 try {
		//nejdříve odstraníme všechny záznamy z vazební tabulky
		$this->auto->odstranExtAuta();
		// v cyklu přidáme pro jednotlivá auta záznamy
		$auta = $this->auto->vypisAuta();
		$vjezdyId = $this->auto->vypisExterniVjezdy()->fetchPairs(NULL, 'id');
		foreach ($auta as $auto) {
		    $key = "_".$auto->id;
		    if(isset($values[$key])){
			$this->auto->pridejExtAuta($auto->id, $values[$key]);
		    }
		}
		
		
		$this->flashMessage('Editace externího vjezu k autu proběhla úspěšně.', 'success');
		$this->redirect('this', $id = null);
	}catch (MyExceptions\AddPdoException $e){
		$this->flashMessage('Při úpravě externích vjezdu nastala chyba.'.$e->getMessage(), 'error');
	}catch (MyExceptions\DelPdoException $e){
		$this->flashMessage('Při úpravě externích vjezdu nastala chyba.'.$e->getMessage(), 'error');
	}
    }
    
}
