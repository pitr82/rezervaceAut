<?php

namespace App\Presenters;

use Nette,
	App\Model,
	App\Components;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var Model\Base */
    protected $base;
    
    public function __construct(Model\Base $base)
    {
        $this->base = $base;
	parent::__construct();
    }
    public function startup() {
	parent::startup();
		
	/*Vytvoření vlastního helperu pro konkrétní template*/
//	$this->template->addFilter('spz', function ($s, $len = 3) {
//	return mb_substr($s, 0, $len);
//    });
    }

    protected function createTemplate($class = NULL)
    {
	$template = parent::createTemplate($class);

	$template->addFilter(NULL, ['OtherHelpers', 'loader']);
	/*Vytvoření vlastního helperu pro všechny template*/
	$template->addFilter('subStr', function ($s, $len = 3) {
	$s = trim($s);
	$sub = mb_substr($s, 0, $len, 'utf-8');
	if($sub != $s)
	    $sub.= '...';
	return $sub;
    });
	return $template;
    }   
    
}
