<?php

namespace App\MyExceptions;

use Nette,
    Exception;
/**
 * Description of MyExceptions
 *
 * @author Petr Stefan
 */
class InvalidTableException extends Exception {} 
class IntegrityConstraintException extends Exception {} 
class UniqueColumnException extends Exception {} 
class DelPdoException extends Exception {} 
class AddPdoException extends Exception {} 
class OtherPdoException extends Exception {} 