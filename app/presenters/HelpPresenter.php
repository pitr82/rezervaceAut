<?php

namespace App\Presenters;

use Nette,
    myLibs\PdfResponse,	
    Nette\Diagnostics\Debugger,
    Nette\Application\Responses;

/**
 * Description of HelpPresenter
 *
 * @author Petr Stefan
 */
class HelpPresenter extends BasePresenter
{
    /**
     * Akce pro stažení pdf souboru
     */
    public function actionDefault($filename)
    {
	Debugger::fireLog('Action');
	/* Response pro zobrazení, pro stažení poslední parametr, TRUE*/
	$this->sendResponse(new Responses\FileResponse($this->context->parameters['wwwDir'].'/files/help.pdf', 'Nápověda.pdf', 'application/pdf', FALSE));
    }
}
