<?php
/*-------------------------------------------------------------------------
 * Create autoloader. You don`t need include file with class.
 * You just need give correct name to class and put it in correct path.
--------------------------------------------------------------------------*/
spl_autoload_register(
	function($class)
		{
		try
			{
			$name=explode('_',$class);
			$name=array_reverse($name);
			$count_name=count($name);
			if($count_name)
				{
				$path=INIT_DIR;
				if(in_array(strtolower($name[0]),array('controller','model','view')))
					{
					$path=ROOT_DIR;
					}
				foreach($name as $dir)
					{
					$path.=DS.strtolower($dir);
					}
				$path.=DS.strtolower($dir).'.php';
				if(file_exists($path))
					{
					include_once($path);
					}
				else
					{
					throw new Error;
					}
				}
			else
				{
				throw new Error;
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	);

$config=Config::Get_Instance()->Get_Config();

include_once(INIT_DIR.DS.'constant'.DS.'constant.php');

new Lang();

new Php_Error();

$db=Db::Get_Instance();

$sql=Sql::Get_Instance();

$session=new Session();

new Controller();
