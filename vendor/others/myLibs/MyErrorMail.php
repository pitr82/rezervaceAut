<?php
namespace myLibs;

/**
 * Description of MyErrorMail
 *
 * @author Petr Stefan
 */
class MyErrorMail extends \Tracy\Logger
{
    public $mailer = array(__CLASS__, 'myDefaultMailer');
    public static $host = null;

    public static function myDefaultMailer($message, $email)
    {
	if( self::$host === null){
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
