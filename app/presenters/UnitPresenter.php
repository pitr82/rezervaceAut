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
 */
class UnitPresenter extends SecurePresenter
{
    /**
     * @inject
     * @var Model\Unit
     */
    public $unit;
    
    
    public function handleEditFirma($id)
    {
	if($utvar = $this->unit->vratFirmu($id))
	$this['formFirma']->setDefaults($utvar);
    }
    
    
    public function handleDeleteFirma($id)
    {
	try {
	    
	    if($this->unit->smazFirmu($id)){
		$this->flashMessage('Firma byla úspěšně odstraněna.', 'success');
	    }else{
		$this->flashMessage('Firma nemohla být odstraněna.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Firma nemohla byt odstraněna: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }
    
    
    public function handleDeleteUtvar($id)
    {
	try {
	    
	    if($this->unit->smazUtvar($id)){
		$this->flashMessage('Útvar byl úspěšně odstraněn.', 'success');
	    }else{
		$this->flashMessage('Útvar nemohl byt odstraněn.', 'error');
	    }
	} catch (MyExceptions\IntegrityConstraintException $ex) {
	    $this->flashMessage('Útvar nemohl byt odstraněn: '.$ex->getMessage(), 'error');
	}
	$this->redirect('this', $id = null);
    }

    public function handleEditUtvar($id)
    {
	if($utvar = $this->unit->vratUtvar($id))
	$this['formUtvar']->setDefaults($utvar);
    }

    
    public function handleUtvarChange($value)
    {
        if ($value) {
            $this['unitCarForm']['auto_id']
                ->setItems($this->unit->vratAuta()->fetchPairs('id', 'spz'))
		->setDefaultvalue($this->unit->autaUtvaru($value));
        } else {
            $this['unitCarForm']['auto_id']->setItems(array());
        }
        $this->redrawControl('tabUnitCar');
    }
    
    
    public function renderList($id) 
    {
        $this->template->firmy = $this->unit->vypisFirmy();
        $this->template->utvary = $this->unit->vypisUtvary();
	$this->template->auta = $this->unit->vratPoleSAuty();
	$this->template->autaBezUtvaru = $this->unit->autaBezUtvaru();
	$this->template->autaNaViceUtvarech = $this->unit->autaNaViceUtvarech();
	// required to enable form access in snippets
        $this->template->_form = $this['unitCarForm'];
    }
    
    public function renderDefault() {
	$this->redirect('Unit:list');
    }
    
    protected function createComponentFormFirma() 
    {
	$form = new Form;
	$form->addText('nazev', 'Název')
		->setRequired('Nezadali jste název útvaru.')
		->setAttribute('placeholder', 'název firmy');
	$form->addText('ico', 'Ičo')
		->setAttribute('placeholder', 'ičo firmy');
	$form->addText('dic', 'Dič')
		->setAttribute('placeholder', 'dič firmy');
	$form->addText('ulice', 'Ulice')
		->setAttribute('placeholder', 'ulice firmy')
		->setRequired('Nezadali jste ulici na níž sídlí firma.');
	$form->addText('mesto', 'Město')
		->setAttribute('placeholder', 'město firmy')
		->setRequired('Nezadali jste město v němž sídlí firma.');
	$form->addText('psc', 'Psč')
		->setAttribute('placeholder', 'psc firmy')
		->addRule(Form::PATTERN, 'PSČ musí mít 5 číslic', '([0-9]\s*){5}')
		->setRequired('Nezadali jste psč firmy.');
	$form->addText('stat', 'stát')
		->setAttribute('placeholder', 'stát firmy')
		->setRequired('Nezadali jstestát ve kterém firma sídlí.')
		->setDefaultvalue('Česká republika');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formFirmaSucceeded;
	return $form;
    }
    
    public function formFirmaSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->unit->addFirma($form->getValues());
		$this->flashMessage('Přidání firmy proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Firma nemohl byt přidán. '.$e->getMessage(), 'error');
		$form->render($form['nazev']->getControlPrototype()->addClass('error'));
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->unit->editFirma($form->getValues(), $id);
		$this->flashMessage('Editace firmy proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci firmy nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    
    protected function createComponentFormUtvar() 
    {
	$form = new Form;
	$form->addText('nazev', 'Název')
		->setRequired('Nezadali jste název útvaru.')
		->setAttribute('placeholder', 'název útvaru');
	$form->addText('popis', 'Popis')
		->setRequired('Nezadali jste podrobnější popis.')
		->setAttribute('placeholder', 'popis útvaru');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formUtvarSucceeded;
	return $form;
    }
    
    public function formUtvarSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try {
		$this->unit->addUtvar($form->getValues());
		$this->flashMessage('Přidání útvaru proběho úspěšně.', 'success');
		$this->redirect('this');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Útvar nemohl byt přidán. '.$e->getMessage(), 'error');
		$form->render($form['nazev']->getControlPrototype()->addClass('error'));
	    }
	}else{
	    //editace - update v databázi
	    try {
		$this->unit->editUtvar($form->getValues(), $id);
		$this->flashMessage('Editace útvaru proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci útvaru nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
    
    protected function createComponentUnitCarForm()
    {
	$utvary = $this->unit->vypisUtvary()->fetchPairs('id', 'nazev');
	$utvarId = key($utvary); // vyber prvni id
	$form = new Form;
	$form->addSelect('utvar_id', 'Útvar',$utvary)
		->setDefaultValue($utvarId);
	$form->addCheckboxList('auto_id', 'Auta', $this->unit->vratAuta()->fetchPairs('id', 'spz'))
		->setDefaultValue($this->unit->autaUtvaru($utvarId));
        $form->addSubmit('send', 'Upravit auta');

        $form->onSuccess[] = $this->processUnitCarForm;
	return $form;
    }
    
    public function processUnitCarForm(Form $form)
    {
        // $form->getValues() ignores invalidated input's values
        $values = $form->getHttpData();
        unset($values['send']);
	 try {
		//nejdříve odstraníme všechna auta z útvaru
		$this->unit->odstranAutaUtvaru($values['utvar_id']);
		// přidáme zaškrtnutá auta, pokud existují
		if(isset($values['auto_id']))
		    $this->unit->pridejAutaUtvaru($values['utvar_id'], $values['auto_id']);
		$this->flashMessage('Editace aut k útvaru proběhla úspěšně.', 'success');
		$this->redirect('this', $id = null);
	}catch (MyExceptions\AddPdoException $e){
		$this->flashMessage('Při úpravě aut k útvaru nastala chyba.'.$e->getMessage(), 'error');
	}catch (MyExceptions\DelPdoException $e){
		$this->flashMessage('Při úpravě aut k útvaru nastala chyba.'.$e->getMessage(), 'error');
	}
    }
}
