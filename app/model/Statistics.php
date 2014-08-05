<?php
namespace App\Model;

use Nette;

/**
 * Description of Statistics
 *
 * @author Petr Stafan
 */
class Statistics extends Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
	$this->database = $database;
    }
    
    
    public function nejPujcAuto()
    {
	return $this->database->table('rezervace')
		->select('count(0) AS counted, auto_id')
		->group('auto_id')
		->order('counted DESC')
		->limit(1);
    }
    
    
    public function nejPujcuje()
    {
	return $this->database->table('rezervace')
		->select('count(*) AS counted, zamestnanec_id')
		->group('zamestnanec_id')
		->order('counted DESC')
		->limit(1);
    }
    
    
    public function nejdelePujcAuto() 
    {	
	return $this->database->table('rezervace')
		->select('sum((timestampdiff(MINUTE,rezervaceOd, rezervaceDo) / 60)) AS soucet, auto_id')
		->group('auto_id')
		->order('soucet DESC')
		->limit(1);
//	pokus s view, nefukční další parametry k count
//	return $this->database->table('vw_autoPujcenoHodin')->select('MAX(soucet) AS soucet, auto_id');
    }
    
    
    public function statsAuta() 
    {	
	$pole = array();
	$dotaz = $this->database->table('rezervace')
		->select("count(*) AS counted, rezervace.auto_id")
		->group('auto_id')
		->order('counted DESC');
//		->order('auto.znackaAuta.znacka');
//		->order('auto:utvar_auto.utvar.nazev');
	foreach ($dotaz as $np){
	    $pole[$np->auto_id]['id'] = $np->auto_id; 
	    $pole[$np->auto_id]['znacka'] = $np->auto->znackaAuta->znacka; 
	    $pole[$np->auto_id]['spz'] = $np->auto->spz; 
	    $pole[$np->auto_id]['counted'] = $np->counted; 
	   
	    $utvary = $np->auto->related('utvar_auto.auto_id');
	    if(count($utvary)){
		foreach ($utvary AS $utvarAuto){
		    $pole[$np->auto_id]['utvar'] = $utvarAuto->utvar->nazev;
		}
	    }else{
		/* pokud auto není v útvaru */
		$pole[$np->auto_id]['utvar'] = 'Nezařazeno';

	    }
        }
	foreach ($this->database->table('vw_autoPujcenoHodin') as $np1){
	    $pole[$np1->auto_id]['soucet'] = $np1->soucet; 
	}
	return $pole;
    }
    
    
    public function statsUziv() 
    {	
	$pole = array();
	$dotaz = $this->database->table('rezervace')
		->select("count(*) AS counted, zamestnanec_id")
		->group('zamestnanec_id')
		->order('counted DESC');
	foreach ($dotaz as $np){
	    $pole[$np->zamestnanec_id]['id'] = $np->zamestnanec_id; 
	    $pole[$np->zamestnanec_id]['jmeno'] = $np->zamestnanec->jmeno; 
	    $pole[$np->zamestnanec_id]['prijmeni'] = $np->zamestnanec->prijmeni; 
	    $pole[$np->zamestnanec_id]['email'] = $np->zamestnanec->email; 
	    $pole[$np->zamestnanec_id]['counted'] = $np->counted; 
	    $pole[$np->zamestnanec_id]['utvar'] = $np->zamestnanec->utvar->nazev;
        }
	foreach ($this->database->table('vw_zamestnanecPujcenoHodin') as $np1){
	    $pole[$np1->zamestnanec_id]['soucet'] = $np1->soucet; 
	}
	return $pole;
    }
    
    
    
//	return $this->database->table('rezervace')
//		->select("id, rezervaceOd, rezervaceDo, TIMESTAMPDIFF(MINUTE, CONCAT(CAST(DATE(rezervaceDo) AS CHAR(25)),' ','15:00:00'), rezervaceDo)/60 AS rozdil")
//		->where("rezervaceOd <= CONCAT(CAST(DATE(rezervaceOd) AS CHAR(25)),' ','15:00:00')")
//		->group('DATE(rezervaceOd)')
//		->having('rozdil > 0')
//		->fetch();
    
    
}
