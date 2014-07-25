<?php
namespace App\Presenters;

use Nette,
    App\Model;

/**
 * Description of StatisticsPresenter
 *
 * @author Petr Stefan 
 */
class StatisticsPresenter extends SecurePresenter 
{
 
    /**
     * @inject
     * @var Model\Statistics
     */
    public $statistics;
    
     /**
     * @inject
     * @var Model\Unit
     */
    public $unit;
    
    public function renderDefault() 
    {
	$this->template->nejPujcAuto = $this->statistics->nejPujcAuto();
	$this->template->nejPujcuje = $this->statistics->nejPujcuje();
	$this->template->nejdelePujcAuto = $this->statistics->nejdelePujcAuto();
	$this->template->statsAuta = $this->statistics->statsAuta();
	$this->template->statsUziv = $this->statistics->statsUziv();
	$this->template->utvary = $this->unit->vypisUtvary();
	$this->template->pSloupcu = \Constants::STATS_DAYS;
    }
    
}
