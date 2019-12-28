<?php 
class ErrorHandler
{
	public static function setErrorHandler($errorTypes)
	{
		set_error_handler(array("ErrorHandler", "errorHandlerCallback"), $errorTypes);
	}
	
	public static function errorHandlerCallback($code, $string, $file, $line, $context)
	{
		throw new ErrorException($string, 0, $code, $file, $line);
	}
	
	public static function setExceptionHandler()
	{
		set_exception_handler(array("ErrorHandler", "exceptionHandlerCallback"));
	}
	
	public static function exceptionHandlerCallback($Exception)
	{
		/*
		 * @todo Exceptions verbergen voor mensen die dat geen snars aangaat? 
		 */
		
		FirePHP::getInstance(true)->error($Exception);
		
		/*
		 * @todo ook nog een keer voor de command line?
		 */
		if(Request::typeAjax())
		{
			echo $Exception;
		}
		else
		{
			new HTTP_Error500(self::formatException($Exception));
		}
	}
	
	protected static function formatException($Exception)
	{
		$formatted = "<div style='text-align:left; '><code>" . nl2br($Exception) . "</code></div>";
		return $formatted;
	}
}
?>