<?php
namespace myLibs;

/**
 * Description of MyErrorMail
 *
 * @author Petr Stefan
 */
class MyLogger extends \Tracy\Logger
{
    /** @var callable handler for sending emails */
	public $mailer = array();

	/** @var string name of the directory where errors should be logged; FALSE means that logging is disabled */
	public $directory;

	/** @var string|array email or emails to which send error notifications */
	public $email;
	
	/** @var string host|domain name */
	public static $host = null;
	
	
	public function __construct($directory, $email, $mailer, $host = null) 
	{
	    $this->directory = $directory;
	    $this->email = $email;
	    $this->mailer = $mailer;
	    self::$host = $host;
	}
	
    /***
    *  Vlastní metoda, umožňuje předat vlastní domain FROM, pokud je vyplněn
    * 
    */
    public static function MyDefaultMailer($message, $email)
    {
	if(self::$host === null){
	    $host = preg_replace('#[^\w.-]+#', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'));
	}else{
	    $host = self::$host;
	}
		$parts = str_replace(
			array("\r\n", "\n"),
			array("\n", PHP_EOL),
			array(
				'headers' => implode("\n", array(
					"From: noreply@$host",
					'X-Mailer: Tracy',
					'Content-Type: text/plain; charset=UTF-8',
					'Content-Transfer-Encoding: 8bit',
				)) . "\n",
				'subject' => "PHP: An error occurred on the server $host",
				'body' => "[" . @date('Y-m-d H:i:s') . "] $message", // @ - timezone may not be set
			)
		);

		mail($email, $parts['subject'], $parts['body'], $parts['headers']);
    }
}
