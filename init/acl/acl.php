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
			global $session;
			$current_methods=self::Get_Current_Methods();
			$class_name=substr($cname,0,-6);
			if(isset($current_methods[$class_name][$mname]))
				{
				if($current_methods[$class_name][$mname]["login_only"])
					{
					if($current_methods[$class_name][$mname]["admin_only"])
						{
						if(!($session->Check('user','is_visible') && 
							$session->Check('user','is_deleted') &&
							$session->Get('user','is_visible') && 
							!$session->Get('user','is_deleted') &&
							$session->Check('user','is_admin') && 
							$session->Get('user','is_admin')))
							{
							throw new Error(ERROR_ACCESS_DENY);
							}
						}
					else
						{
						if(!($session->Check('user','is_visible') && 
							$session->Check('user','is_deleted') &&
							$session->Get('user','is_visible') && 
							!$session->Get('user','is_deleted')))
							{
							throw new Error(ERROR_ACCESS_DENY);
							}
						}
					
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Read and return array with config
 *
 * Config::Get_Instance()->Get_Config();
 *
 * Return: array with config.
--------------------------------------------------------------------------*/
	public static function Get_Db_Methods()
		{
		try
			{
			global $sql;
			$methods_data=$sql->Select(array("tablename"=>'acl',
											"fields"=>array('id','class_name','method_name','login_only','admin_only')));
			foreach($methods_data as $db_method)
				{
				$db_methods[$db_method["class_name"]][$db_method["method_name"]]=array("id"=>$db_method["id"],"login_only"=>$db_method["login_only"],"admin_only"=>$db_method["admin_only"]);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $db_methods;
		}



/*-------------------------------------------------------------------------
 * Read and return array with config
 *
 * Config::Get_Instance()->Get_Config();
 *
 * Return: array with config.
--------------------------------------------------------------------------*/
	public static function Get_Current_Methods()
		{
		try
			{
			$skipped_dirs=array('.','..','pattern');
			$skipped_methods=array('__construct','__call','Check');

			foreach(scandir(ROOT_DIR.DS.'model') as $dirname)
				{
				$path=ROOT_DIR.DS.'model/'.$dirname.'/';
				if(is_dir($path) && !in_array($dirname,$skipped_dirs))
					{
					$filename=$path.$dirname.'.php';
					if(file_exists($filename))
						{
						include_once($filename);
						$classname=ucwords($dirname).'_Model';
						if(class_exists($classname))
							{
							if($reflection = new ReflectionClass($classname))
								{
								foreach($reflection->getMethods() as $method)
									{
									if(!in_array($method->name,$skipped_methods))
										{
										$db_acl=self::Get_Db_Methods();
										$cname=ucwords($dirname);
										$mname=$method->name;
										if(isset($db_acl[$cname][$mname]))
											{
											$methods[$cname][$mname]=$db_acl[$cname][$mname];
											}
										else
											{
											$methods[$cname][$mname]=array("id"=>-1,"login_only"=>0,"admin_only"=>0);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $methods;
		}
	}