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
 * Redefine behaviour php error
--------------------------------------------------------------------------*/


class Php_Error
	{
/*-------------------------------------------------------------------------
* Constructor of Php_Error class.
* Define function wich will be responsibile to php error
--------------------------------------------------------------------------*/
    public function __construct()
		{
//caught error
        set_error_handler(array($this,'Other_Error_Catcher'));
//caught fatal error
        register_shutdown_function(array($this,'Fatal_Error_Catcher'));
        $this->config=Config::Get_Instance()->Get_Config();
		}



/*-------------------------------------------------------------------------
* Function wich will be responsibile to php error
--------------------------------------------------------------------------*/
    public function Other_Error_Catcher($errno, $errstr, $errfile, $errline)
		{
		$log="Error: $errno in file $errfile on line $errline - $errstr";
		$error=new Error($log);
		$error->Error();
		}



/*-------------------------------------------------------------------------
* Function wich will be responsibile to php fatal error
--------------------------------------------------------------------------*/
    public function Fatal_Error_Catcher()
		{
        $error=error_get_last();
        if(is_array($error))
			{
			if($this->config['log'])
				{
				$log="Fatal Error: ".$error['type']." in file ".$error['file']." on line ".$error['line']." - ".$error['message'];
				Log::Set_Error($log);
				}
			Redirect::Page('page/error/500');
			}
		}
	}