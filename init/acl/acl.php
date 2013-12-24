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
 * Access
 *
 * Acl::Access(Model);
--------------------------------------------------------------------------*/


class Acl
	{
	//private static $instance;
	private function __construct()
		{}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Read and return array with config
 *
 * Config::Get_Instance()->Get_Config();
 *
 * Return: array with config.
--------------------------------------------------------------------------*/
	public static function Access($cname,$mname,$args='')
		{
		try
			{
			if($cname=='Admin_Model')
				{
				if($mname=='Menu')
					{
					//throw new Error('Access deny');
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}
	}