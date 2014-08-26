<?php

/**
 * Hlavní třída pro modely
 *
 * @author Petr Stefan
 */
namespace App\Model;

use Nette;

class Base extends Nette\Object{
    
    /** @var Nette\Database\Context */
    protected $database;
    
    /** Služby pro vracení objektu */
    protected $services = array();  // pole objektu
    
    
    /**
     * 
     * @param \Nette\Database\Context $db
     */
    public function __construct(\Nette\Database\Context $db) 
    {
	$this->database = $db;
    }
    
    /**
     * Funkce pro vráceni výpisu aut (vytváří se jen jednou)
     * 
     * @return object
     */
    public function vypisAuta() 
    {
	if (!isset($this->services['vypisAuta'])) {
            $this->services['vypisAuta'] = $this->createVypisAuta();
        }
        return $this->services['vypisAuta'];
    }
    /** 
     * Funkce vratí Výpis aut z tabulky auto
     * 
     * @return object
     */
    private function createVypisAuta()
    {
	return  $this->database->table('auto')->order('znackaAuta.znacka');
    }
    /**
     * Funkce pro vráceni výpisu utvaru, pokud není vloženo id, vypiše všechny
     *  (vytváří se jen jednou)
     * 
     * @param int $userId   -- id útvaru | textový řetězec [all]
     * @return object
     */
    public function vypisUtvarZamestnance($id) 
    {
	if (is_int($id))
	    return $this->database->table('utvar')->get($id);
	else
	    return $this->database->table('utvar')->order('nazev');
    }
    
   
   
    public function checkUser($userId)
    {
	$zamestnanec = $this->database->table('zamestnanec')->get($userId);
	if($zamestnanec->povolen == 0 || $zamestnanec->zruseno == 1)
	    return false;
	else
	    return true;
    }
    
    public function onlineUpdate($userId)
    {
	$this->database->table('zamestnanec')->where('id', $userId)->update(array('online' => $this->database->literal("NOW()")));
    }
    
    public function onlioneShow() {
	return $this->database->table('zamestnanec')->where('`online` >= DATE_SUB(NOW(), INTERVAL ? MINUTE)', 5);
    }
    
}
