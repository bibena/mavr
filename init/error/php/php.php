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


class Php_Error
	{
	public function __construct()
		{
		set_error_handler(array($this, 'Other_Error_Catcher'));

		set_exception_handler(array($this, 'Uncaught_Exception_Catcher'));

		register_shutdown_function(array($this, 'Fatal_Error_Catcher'));

		ob_start();
		}

	public function Uncaught_Exception_Catcher($exception)
		{
		ob_end_clean();

		global $config,$session;

		$error_message=$log_message='Uncaught Exception: '.$exception->getCode().' in file '.$exception->getFile().' on line '.$exception->getLine().' with message "'.$exception->getMessage().'"';
		$log_message.="\n\tTrace:\n\t".$exception->getTraceAsString();
		if($config["full_error_info"])
			{
			$error_message.="\n\tTrace:\n\t".$exception->getTraceAsString();
			}
		if($config["log"])
			{
			Log::Set_Error($log_message);
			}
		$page=new Page_Controller;
		if($config["show_error"])
			{
			$session->Set_Error(nl2br($error_message));
			}
		Redirect::Page('page/error/500');
		}



	public function Other_Error_Catcher($errno,$errstr,$errfile,$errline)
		{
		global $config,$session;

		if($config["log"])
			{
			$log_message='Error: '.$errno.' in file '.$errfile.' on line '.$errline.' with message "'.$errstr.'"';
			Log::Set_Info($log_message);
			}
		}



	public function Fatal_Error_Catcher()
		{
		$error = error_get_last();
		if (isset($error))
			{
			if($error["type"] == E_ERROR
			|| $error["type"] == E_PARSE
			|| $error["type"] == E_COMPILE_ERROR
			|| $error["type"] == E_CORE_ERROR)
				{
				ob_end_clean();

				global $config,$session;

				$error_message=$log_message='Fatal error: '.$error["type"].' in file '.$error["file"].' on line '.$error["line"].' with message "'.$error["message"].'"';

				if($config["log"])
					{
					Log::Set_Error($log_message);
					}
				$page=new Page_Controller;
				if($config["show_error"])
					{
					$session->Set_Error(nl2br($error_message));
					}
				Redirect::Page('page/error/500');
				}
			else
				{
				ob_end_flush();
				}
			}
		else
			{
			ob_end_flush();
			}
		}
	}
