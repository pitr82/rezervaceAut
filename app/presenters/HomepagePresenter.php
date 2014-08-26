<?php

namespace App\Presenters;

use Nette,
    App\Components;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    /** @persistent */
    public $backlink = '';      
    
    public function renderDefault()
    {	
    }

    protected function createComponentTabRezervaci()
    {
        $trezervace = new Components\TabRezervaciControl($this->base, date('Y-m-d'), 'all');
        return $trezervace;
    }
    
    /**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Login:')
			->setAttribute('Placeholder', 'Login..')
			->setRequired('Zadejte svůj login.');

		$form->addPassword('password', 'heslo:')
			->setAttribute('Placeholder', 'Heslo..')
			->setRequired('Zadejte své heslo');

		$form->addCheckbox('remember', 'Trvale');

		$form->addSubmit('send', 'Přihlásit se');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	public function signInFormSucceeded($form)
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('1 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			/* pokud uživatel byl odhlášen za inaktivitu, tak přeeměrovat zpět */
			$this->restoreRequest($this->backlink);
			$this->redirect('Reserve:default', array('datum' => date('Y-m-d')) );

			

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Vaše odhlášení proběhlo úspěšně', 'success');
		$this->redirect('Homepage:default');
	}
    
}