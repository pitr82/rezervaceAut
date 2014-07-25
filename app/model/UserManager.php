<?php

namespace App\Model;

use Nette,
	Nette\Utils\Strings,
	Nette\Security\Passwords,
	App\MyExceptions;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'zamestnanec',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'login',
		COLUMN_PASSWORD_HASH = 'heslo',
		COLUMN_ROLE = 'role',
	/*další řádky tabulky*/
		COLUMN_FORNAME = 'jmeno',
		COLUMN_LASTNAME = 'prijmeni',
		COLUMN_COMPANY = 'firma_id',
		COLUMN_EMAIL = 'email',
		COLUMN_ENABLE = 'povolen',
		COLUMN_VISIBLE = 'visible',
		COLUMN_CREATED = 'vytvoreno',
		COLUMN_DELETE = 'zruseno';
		
			
	


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)
			->where(self::COLUMN_DELETE, 0)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Špatné uživatelské jméno.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('Špatné uživatelské heslo.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		} elseif ($row[self::COLUMN_ENABLE] == '0'){
			throw new Nette\Security\AuthenticationException('Přístup do IS rezervace Vám byl zablokován. Kontaktujte podporu.', self::NOT_APPROVED);
		
		} elseif ($row[self::COLUMN_DELETE] == '1'){
			throw new Nette\Security\AuthenticationException('Uživatel byl zrušen.', self::NOT_APPROVED);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param  array    Pole hodnot z formuláře
	 * @return bool
	 */
	public function addZamestnanec($data)
	{
	    //odstraníme kontrolní heslo
	    unset($data['heslo2']);
	    // vygenerujeme heslo
	    $data[self::COLUMN_PASSWORD_HASH] = Passwords::hash($data[self::COLUMN_PASSWORD_HASH]);
	    // upravime datum 
	    $data[self::COLUMN_CREATED] = new Nette\Database\SqlLiteral('NOW()');
	    //kontrola emailu, jestli není zamestnanec jiz v databazi
	    try{
		$this->database->table(self::TABLE_NAME)->insert($data);
	    } catch (\PDOException $e){
		throw new MyExceptions\UniqueColumnException("Email, nebo login zaměstnance již jsou v databázi");
	    }
	    return true;
	}
	
	public function editZamestnance($data, $idZamestnance)
	{
	    //odstraníme kontrolnní heslo
	    unset($data['heslo2']);
	    $sql = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID.' = ?', $idZamestnance);
	    if(count($sql)){
		$zamestnanec = $this->database->table(self::TABLE_NAME)->get($idZamestnance);
		//neměnit heslo, pokud nebylo vyplněno, jinak zašifrujeme
		if($data[self::COLUMN_PASSWORD_HASH]== '')
		    unset($data['heslo']);
		else
		    $data[self::COLUMN_PASSWORD_HASH] = Passwords::hash($data[self::COLUMN_PASSWORD_HASH]);
		try {
		    $zamestnanec->update($data);
		} catch (\PDOException $e){
		    
		    if ((int)$e->errorInfo[1] === 1062)
			throw new MyExceptions\UniqueColumnException("Email, nebo login zaměstnance již jsou v databázi");	
		    else
			throw new MyExceptions\OtherPdoException("Nastala jiná chyba.");	

		}
		return true;    
	    }else{
		return false;
	    }
	}

	

	/**
	* Funkce vypsání zaměstnanců
	* 
	* @return object
	*/
       public function vypisZamestnance() 
       {
	   return  $this->database->table('zamestnanec')->order('prijmeni ASC');
       }
       /**
	* Funkce pro vypsání dat o zaměstnanci
        * @paramm int	-id uživatele
	* @return object
	*/
	public function vratZamestnance($id) 
       {
	   return  $this->database->table('zamestnanec')->get($id);
       }
       /**
	* Funkce vypsání společností
	* 
	* @return object
	*/
       public function vypisFirmu() 
       {
	   return  $this->database->table('firma')->order('nazev ASC');
       }
       
}
