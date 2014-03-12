<?php

try{
	if(!defined('FLAG'))
		{
		throw new Flag_Error();
		}
	}
catch (Flag_Error $e)
	{
	$e->Error();
	}

/*-------------------------------------------------------------------------
 * Define class Error which extends Exception
--------------------------------------------------------------------------*/


class Error extends Exception
	{
/*-------------------------------------------------------------------------
* Constructor of Error class
--------------------------------------------------------------------------*/
	public function __construct($message=ERROR_STANDART_MESSAGE, $code=0, Exception $previous=null)
		{
		parent::__construct($message, $code, $previous);
		}



/*-------------------------------------------------------------------------
* Display standart 500-error page
*
* $error->Error();
*
* Display: standart 500-error page.
--------------------------------------------------------------------------*/
    public function Error($number=500)
		{
		try
			{
			global $session,$config;
			if(is_numeric($number))
				{
				$error=$this->Create_String();
				if($config['log'])
					{
					Log::Set_Error($error);
					}
				$page=new Page_Controller;
				if($config['show_error'])
					{
					$session->Set_Error(nl2br($error));
					$page->Error($number,nl2br($error));
					}
				else
					{
					$session->Set_Error('');
					$page->Error($number);
					}
				die();
				}
			else
				{
				throw new Error(ERROR_IN_ERROR_CATCHER);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
* Create error string
*
* $error->Create_String();
*
* Return: string string with message.
--------------------------------------------------------------------------*/
	protected function Create_String()
		{
		global $config;
		$error=get_class($this).": ".parent::getCode()." in file ".parent::getFile()." on line ".parent::getLine()." - ".parent::getMessage();
		if($config['full_error_info'])
			{
			$error.="\n\tTrace:\n\t".serialize(parent::getTrace());
			}
		return $error;
		}
	}
