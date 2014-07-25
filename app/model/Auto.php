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
    
}
