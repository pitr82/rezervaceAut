<?php

namespace App\Model;

use Nette,
	App\MyExceptions;

/**
 * Description of Auto
 *
 * @author Petr Stefan
 */
class Auto extends \Nette\Object 
{
   /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
	    $this->database = $database;
    }
    
    
    public function vypisAuta()
    {
	return $this->database->table('auto');
    }
    
    public function vypisAutaPoldeUtvaru()
    {
	return $this->database->table('auto')
		->select('`auto`.`id`, `auto`.`spz`')
		->select('`utvar`.`nazev`')
		->order(':utvar_auto.utvar.nazev');
    }
    
    public function vratAuto($id)
    {
	return $this->database->table('auto')->get($id);
    }
    
    public function vypisZnacky() {
	return $this->database->table('znackaAuta')->order('znacka ASC');
    }
       
    public function vypisTypy() {
	return $this->database->table('typAuta')->order('typ ASC');
    }
   
    public function vypisFirmy() {
	return $this->database->table('firma')->order('nazev ASC');
    }

    public function vypisExterniVjezdy() {
	return $this->database->table('externiVjezd')->order('nazev ASC');
    }
    
    public function addAuto($data)
    {
	try {
	    $this->database->table('auto')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Toto auto již zřejmě existuje.");
	}
	return true;
    }

            
    public function editAuto($data,$id)
    {
	try {
	    $dotaz = $this->database->table('auto')->get($id);
	    $dotaz->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Auto již zřejmě existuje nebo máte špatnou spz.");
	}
	
    }
    
    
    public function smazAuto($id)
    {
	try {
	    $dotaz = $this->database->table('auto')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Auto může obsahovat vazby na rezervaci, utvar.");
	}
	return $dotaz;
    }
    
    public function vratZnacka($id)
    {
	return $this->database->table('znackaAuta')->get($id);
    }
    
    
    public function addZnacka($data)
    {
	try {
	    $this->database->table('znackaAuta')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Toto značka již zřejmě existuje.");
	}
	return true;
    }
    
    public function editZnacka($data,$id)
    {
	try {
	    $dotaz = $this->database->table('znackaAuta')->get($id);
	    $dotaz->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Značka již zřejmě existuje.");
	}
	
    }
    
     public function smazZnacka($id)
    {
	try {
	    $dotaz = $this->database->table('znackaAuta')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Značka může obsahovat vazby na auto.");
	}
	return $dotaz;
    }
    
    public function vratTyp($id)
    {
	return $this->database->table('typAuta')->get($id);
    }
    
    
    public function addTyp($data)
    {
	try {
	    $this->database->table('typAuta')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Tento typ již zřejmě existuje.");
	}
	return true;
    }
    
    public function editTyp($data,$id)
    {
	try {
	    $dotaz = $this->database->table('typAuta')->get($id);
	    $dotaz->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Typ již zřejmě existuje.");
	}
    }
    
     public function smazTyp($id)
    {
	try {
	    $dotaz = $this->database->table('typAuta')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Typ může obsahovat vazby na auto.");
	}
	return $dotaz;
    }
    
    public function vratExterniVjezd($id)
    {
	return $this->database->table('externiVjezd')->get($id);
    }
        
    public function addExterniVjezd($data)
    {
	try {
	    $this->database->table('externiVjezd')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Tento externí vjezd již existuje.");
	}
	return true;
    }
    
     public function editExterniVjezd($data,$id)
    {
	try {
	    $dotaz = $this->database->table('externiVjezd')->get($id);
	    $dotaz->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Typ již zřejmě existuje.");
	}
    }
    
     public function smazExterniVjezd($id)
    {
	try {
	    $dotaz = $this->database->table('externiVjezd')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Externí vjezd může obsahovat vazby na auto.");
	}
	return $dotaz;
    }
    
     public function externiVjezdAuta($autoId) 
    {
	$auto = $this->database->table('auto')->get($autoId);
	$externiVjezdy = array();
	foreach ($auto->related('auto_externiVjezd.externiVjezd_id') as $a){
	    $externiVjezdy[] = $a->externiVjezd_id;
	}
	return $externiVjezdy;
    }
    
    public function odstranExtAuta($autoId)	
    {
	try {
	    $this->database->table('auto_externiVjezd')->where('auto_id', $autoId)->delete();
	} catch (\PDOException $e) {
	    throw new MyExceptions\DelPdoException("Chyba při odstraňování");
	}
    }
    
    public function pridejExtAuta($autoId, array $extVjezdy)
    {
	$poleInsertu = array();
	try {
	    foreach ($extVjezdy as $extVjezd) {
		$poleInsertu[]=array('auto_id' => $autoId, 'externiVjezd_id' => $extVjezd);
	    }
	    $this->database->table('auto_externiVjezd')->insert($poleInsertu);
	} catch (\PDOException $e) {
	    throw new MyExceptions\AddPdoException("Chyba při přidávání".$e->getMessage());
	}
    }
    
}
