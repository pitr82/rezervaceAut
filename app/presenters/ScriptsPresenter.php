<?php
namespace App\Presenters;

use Nette,
    App\Model;

/**
 * Description of ScriptsPresenter
 *
 * @author Petr Stefan
 */
class ScriptsPresenter extends BasePresenter 
{

    /** 
     * @inject
     * @var Model\Scripts 
     */
    public $Scripts;
    
    public function renderDefault()
    {
	/* přesměrování na  error 404 */
	throw new Nette\Application\ForbiddenRequestException;
    }
    
    
    public function renderNotificationEmail2f0714f5365318775c8f50d720a307dc()
    {
	$host = $this->context->httpRequest->url->hostUrl;
	$path = $this->context->httpRequest->url->basePath;
	$this->Scripts->checkRezervationForNotification($host.$path);
	
//	$this->setView('reserveMailNtf');
	/*  ukončení presenteru */
	$this->presenter->terminate();
    }
    
}
