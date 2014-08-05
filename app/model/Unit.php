<?php
/**
 * Unit management
 *
 * @author Petr Stefan
 */
namespace App\Model;
	    

use Nette,
	App\MyExceptions;


class Unit extends \Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;

    /** Služby pro vracení objektu */
    protected $services = array();  // pole objektu
    

    public function __construct(Nette\Database\Context $database)
    {
	    $this->database = $database;
    }
    
    
     public function vypisFirmy()
    {
        return $this->database->table('firma')->order('nazev ASC');
    }
    
    
    public function addFirma($data)
    {
	try {
	    $this->database->table('firma')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Tato firma již zřejmě existuje.");
	}
	return true;
    }

    
    public function vratFirmu($id) 
    {
	    return $this->database->table('firma')->get($id);
    }
    
    
    public function editFirma($data,$id)
    {
	try {
	    $firma = $this->database->table('firma')->get($id);
	    $firma->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Firma již zřejmě existuje nebo máte šptné ičo, dič.");
	}
	
    }
    
    
    public function smazFirmu($id)
    {
	try {
	    $dotaz = $this->database->table('firma')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Firma může obsahovat vazby na zaměstnance, automobily.");
	}
	return $dotaz;
    }
    
    public function createVypisUtvary()
    {
	try {
	    $dotaz =  $this->database->table('utvar')->order('nazev ASC');
	} catch (\Exception $e) {
	    if ($e->getCode()== "42S02"){
		throw new MyExceptions\InvalidTableException("Nenalezena tabulka.");
	    }else{
		throw new \Exception("Chyba databaze.", 0, $e);
	    }
	}
	return $dotaz;
    }
    
    public function vypisUtvary()
    {
	if (!isset($this->services['vypisUtvary'])) {
            $this->services['vypisUtvary'] = $this->createVypisUtvary();
        }
        return $this->services['vypisUtvary'];
    }

   
    
    public function smazUtvar($id)
    {
	try {
	    $dotaz = $this->database->table('utvar')->where('id', $id)->delete();
	}catch (\PDOException $e) {
	    throw new MyExceptions\IntegrityConstraintException("Utvar může obsahovat vazby na zaměstnance, automobily.");
	}catch (\Exception $e) {
	    throw new \Exception("Chyba databáze.", 0, $e);
	}
	return $dotaz;
    }
    
    public function addUtvar($data)
    {
	try {
	    $this->database->table('utvar')->insert($data);
	    
	} catch (\PDOException $e){
	    throw new MyExceptions\UniqueColumnException("Tento útvar již zřejmě existuje.");
	} catch (\Exception $e) {
	    throw new \Exception("Chyba databáze.", 0, $e);
	}
	return true;
    }
    
    public function editUtvar($data,$id)
    {
	try {
	    $utvar = $this->database->table('utvar')->get($id);
	    $utvar->update($data);
	} catch (\PDOException $e) {
	    throw new MyExceptions\UniqueColumnException("Tento útvar již zřejmě existuje.");
	}
	
    }
    
    public function vratUtvar($id) 
    {   
	return $this->database->table('utvar')->get($id);
    }
    
    public function vratAuta() 
    {
	return $this->database->table('auto')->select('*')->order('spz ASC');
    }
    
    
    /**
     * Funkce vrací pole, kde pro každé id auta jsou vypsány různé parametry
     * Využívá se v Unit:list, jako doplněk informací k formuláři spřaženo podle id chechlistu
     * 
     * @return array
     */
    public function vratPoleSAuty() {
	$poleAut = array();
	foreach($this->vratAuta() as $auto){
	    $poleAut[$auto->id] = array('firma' => $auto->firma->nazev, 
					'typAuta' => $auto->typAuta->typ,
					'znackaAuta' => $auto->znackaAuta->znacka,
					'popis' => $auto->popis,
		    );
	}
	return $poleAut;
    }
    
   
    public function autaUtvaru($utvarId) 
    {
	$utvar = $this->database->table('utvar')->get($utvarId);
	$auta = array();
	foreach ($utvar->related('utvar_auto.utvar_id') as $ut){
	    $auta[] = $ut->auto_id;
	}
	return $auta;
    }
    
    public function odstranAutaUtvaru($utvarId)	
    {
	try {
	    $this->database->table('utvar_auto')->where('utvar_id', $utvarId)->delete();
	} catch (\PDOException $e) {
	    throw new MyExceptions\DelPdoException("Chyba při odstraňování");
	}
    }
    
    
    public function pridejAutaUtvaru($utvarId, array $auta)
    {
	$poleInsertu = array();
	try {
	    foreach ($auta as $auto) {
		$poleInsertu[]=array('utvar_id' => $utvarId, 'auto_id' => $auto);
	    }
	    $this->database->table('utvar_auto')->insert($poleInsertu);
	} catch (\PDOException $e) {
	    throw new MyExceptions\AddPdoException("Chyba při přidávání".$e->getMessage());
	}
    }
    
    public function autaBezUtvaru() 
    {
	$autosId =array();
	foreach ($this->database->table('utvar_auto')->select('auto_id') as $utvar_auto) {
	   $autosId[] = $utvar_auto->auto_id;
	}
	return	$this->database->table('auto')->where('id NOT', $autosId );
    }
    
    public function autaNaViceUtvarech() 
    {
	return $this->database->table('utvar_auto')->group('auto_id')->having('count(auto_id) > 1');
    }
}
