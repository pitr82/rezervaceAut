<?php

/**
 * Description of TRezervaceControl
 *
 * @author petr
 */
namespace App\Components;

use Nette\Application\UI,
    App\Model;


/**
* komponenta vytvaření tabulky pro rezervace
*/
class TabRezervaciControl extends UI\Control
{
       
    
    /**  var @var \App\Model\Base */
    private $base;
    private $den;
    private $userId;
    private $rozsah;
    private $interval;
    



    /**
 * 
 * @param \App\Model\Base $base	    --Di na model Base
 * @param int $rozsah		    --rozsah tabulky [hodiny]
 * @param int $interval		    --nejmenší interval rezervace [hodiny]
 */
    public function __construct(\App\Model\Base $base, $den, $userId = 'all', $rozsah = 24, $interval = 0.5) {
	$this->base = $base;
	$this->den = $den;
	$this->userId = $userId;
	$this->rozsah = $rozsah;
	$this->interval = $interval;
    }

    public function render()
    {
	$template = $this->template;
	$template->setFile(__DIR__ . '/TabRezervaciControl.latte');
	// vložení helperu
//	$template->addFilter('shortify', function ($s) {
//        return mb_substr($s, 0, 2); // zkrátí text na 10 písmen
//	});	
// vložíme do šablony nějaké parametry
	/*interval v minutach*/
	$template->interval = $this->interval*60;
	/*rozsah v hodinach*/
	$template->rozsah = $this->rozsah;
	$template->den = $this->den;
	$template->pocetSloupcu = ($this->rozsah)/($this->interval);
	// seznam aut z \App\Model\Base
	$template->utvar = $this->base->vypisUtvarZamestnance($this->userId);
	// a vykreslíme ji
	$template->render();
    }
}
