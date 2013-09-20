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
 * Read config file
 *
 * Config::Get_Instance()->Get_Config();
--------------------------------------------------------------------------*/


class Config
	{
	private static $instance;
	private $config;
	private function __construct()
		{}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Return single copy of config
 *
 * Config::Get_Instance();
 *
 * Return: Config single copy of class.
--------------------------------------------------------------------------*/
	public static function Get_Instance()
		{
		if(is_null(self::$instance))
			{
			self::$instance=new Config();
			}
		return self::$instance;
		}



/*-------------------------------------------------------------------------
 * Read and return array with config
 *
 * Config::Get_Instance()->Get_Config();
 *
 * Return: array with config.
--------------------------------------------------------------------------*/
	public function Get_Config()
		{
		try
			{
			if(!$this->config=parse_ini_file(ROOT_DIR.'/config'))
				{
				throw new Error('Error reading config file');
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->config;
		}
	}