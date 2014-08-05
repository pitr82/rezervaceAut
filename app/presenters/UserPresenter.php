<?php
namespace App\Presenters;

use Nette,
    App\Model,
    myLibs\VisualPaginator,
    Nette\Application\UI\Form,
    App\MyExceptions;

/**
 * Description of UserPresenter
 *
 * @author Petr Stefan
 */

class UserPresenter extends AdminPresenter
{
    /**
     * @inject
     * @var Model\UserManager
     */
    public $userManager;

  
    public function handleDelete($id)
    {
	if($this->userManager->smazUzivatele($id)){
	    $this->flashMessage('Uživatel byl úspěšně odstraněn', 'success');
	}else{
	    $this->flashMessage('Uživatel nemohl byt odstraněn', 'error');
	}
	$this->redirect('this');
	    
    }

    public function handleEdit($id)
    {
	if($zamestnanec = $this->userManager->vratZamestnance($id))
	    $this['formUser']->setDefaults($zamestnanec);
	if($this->isAjax())
	    $this->redrawControl ('editUser');
    }
    
    public function RenderDefault()
    {
	$this->redirect('User:list');
    }
    
    public function RenderList($id)
    {
	$sql = $this->userManager->vypisZamestnance();
	// vytvoření komponenty vp - VisualPaginator dědí z control
	$vp = new VisualPaginator($this, 'paginator');
	$paginator = $vp->getPaginator();
        $paginator->itemCount = $sql->count('*');
	$paginator->itemsPerPage = 40;

	if(!$this->getParameter('page'))
	    $paginator->Page = 1;
	else
	    $paginator->Page = $this->getParameter('page');
	
	$this->template->zamestnanci = $sql->limit($paginator->getLength(), $paginator->getOffset());
    }
    
    
    protected function createComponentFormUser() 
    {
	$id = $this->getParameter('id');
	$form = new Form;
	$poleFirem = $this->userManager->vypisFirmu()->fetchPairs('id', 'nazev');
	$form->addSelect('firma_id', 'Firma',$poleFirem)
		->setRequired('Vyberte firmu zaměstnance');
	$poleUtvaru = $this->base->vypisUtvarZamestnance('all')->fetchPairs('id', 'nazev');
	$form->addSelect('utvar_id', 'Útvar',$poleUtvaru)
		->setRequired('Vyberte útvar zaměstnance');
	$form->addSelect('povolen', 'Povolen',array('0' => 'Ne', '1' => 'Ano'))
		->setDefaultValue('1');
	$form->addText('jmeno', 'Jméno')
		->setRequired('Nezadali jste jméno zaměstnance.')
		->setAttribute('placeholder', 'jméno zaměstnance');
	$form->addText('prijmeni', 'Příjmení')
		->setRequired('Nezadali jste příjmení zaměstnance.')
		->setAttribute('placeholder', 'příjmení zaměstnance');
	$form->addText('email', 'Email')
		->setType('email')
		->setAttribute('placeholder', 'napište email')
		->setRequired('Nezadali jste email.')
		->addRule(FORM::EMAIL,'Nemáte správně vyplněný emial');
	$form->addText('login', 'Login')
		->setRequired('Nezadali jste login zaměstnance.')
		->setAttribute('placeholder', 'login zaměstnance');
	
	if($id){
	    $form->addText('heslo', 'Heslo')
		// nebudeme požadovat heslo ->setRequired('Nezadali jste heslo.')
		->setType('password');
	}else{
	    $form->addText('heslo', 'Heslo')
		->setRequired('Nezadali jste heslo.')
		->setType('password');
	}
	$form->addText('heslo2', 'Ověření hesla')
		->setType('password')
		->addRule(Form::EQUAL, 'Zadané hesla se neschodijí', $form['heslo']);
	$form->addSelect('role', 'Role', array('zamestnanec' => 'Zaměstnanec','vedouci' => 'Vedoucí','admin' => 'Admin'))
		->setDefaultValue('zamestnanec');
	$form->addSubmit('send', 'Přidej/edituj');
	
	$form->onSuccess[] = $this->formUserSucceeded;
	
	return $form;
    }
    
    public function formUserSucceeded($form)
    {
	$id = $this->getParameter('id');
	if(!$id){
	    // přidávání do databáze
	    try{
		$this->userManager->addZamestnanec($form->getValues());
		$this->flashMessage('Přidání zaměstnance proběho úspěšně.', 'success');
		$this->redirect('User:list');
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při přidávání zaměstnance nastala chyba. '.$e->getMessage(), 'error');
	    }
		
	}else{
	    //editace - update v databázi
	    try{
		$this->userManager->editZamestnance($form->getValues(), $id);
		$this->flashMessage('Editace zaměstnance proběhla úspěšně.', 'success');
		//vymažeme id
		$this->redirect('this', $id = null);
	    }catch (MyExceptions\UniqueColumnException $e){
		$this->flashMessage('Při editaci zaměstnance nastala chyba. '.$e->getMessage(), 'error');
	    }  catch (MyExceptions\OtherPdoException $e){
		$this->flashMessage('Při editaci zaměstnance nastala chyba. '.$e->getMessage(), 'error');
	    }
	}
    }
}
