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
 * Singletone Factory Db
 *
 * Return: Db single copy of db driver.
--------------------------------------------------------------------------*/

class Db
	{
	private static $instance;
	private function __construct()
		{}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Db::Get_Instance();
 *
 * Return: Db single copy of db driver.
--------------------------------------------------------------------------*/
	public static function Get_Instance()
		{
		if(is_null(self::$instance))
			{
			$config=Config::Get_Instance()->Get_Config();
			$classname=ucfirst($config['dbtype'].'_'.__CLASS__);
			$db=new $classname;
			self::$instance=$db->Get_PDO();
			}
		return self::$instance;
		}
	}
