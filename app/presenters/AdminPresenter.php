<?php

namespace App\Presenters;

use Nette;

/**
 * Description of AdminPresenter
 *
 * @author Petr Stefan
 */
class AdminPresenter extends SecurePresenter
{
    public function startup()
    {
	parent::startup();
	/* Omezení přístupu na administrátory */
	if ($this->user->identity->role == 'zamestnanec') {
	    $this->redirect('Homepage:');
	}
    }
}
