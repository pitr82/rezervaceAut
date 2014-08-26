<?php

namespace App\Presenters;

use Nette;
/**
 * Description of SecurePresenter
 *
 * @author Petr Stefan
 */
class SecurePresenter extends BasePresenter
{
    
    public function startup() {
	parent::startup();
	
	/* kontrola neaktivity */
	if (!$this->user->isLoggedIn()) {
		if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
			$this->flashMessage('Byli jste automaticky odhlášení z důvodu neaktivity.');
		}
		$this->redirect('Homepage:', array('backlink' => $this->storeRequest()));
	}
	/* kontrola uživatele za zablokování, zrušení */ 
	elseif(!$this->base->checkUser($this->user->getId())){
	    $this->flashMessage('Byli jste automaticky odhlášení z důvodu zablokování.');
	    $this->user->logout();
	    $this->redirect('Homepage:', array('backlink' => $this->storeRequest()));
	/* pokud je vše v pořádku*/	    
	}else{
	    $this->base->onlineUpdate($this->user->getId());
	    // povinný parametr po přihlášení
	    barDump($this->base->onlineShow()) ;
	    $this->template->onlineUsers = $this->base->onlineShow();
	    
	}
    }
}
