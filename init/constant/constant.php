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
$config=Config::Get_Instance()->Get_Config();
if($config['subdir']!=='')
	{
	define('SUB_DIR',DS.$config['subdir'].DS);
	}
else
	{
	define('SUB_DIR',DS);
	}
define('TITLE',$config['title']);
define('IS_LOGIN',isset($_SESSION['user'])?true:false);
define('IS_ADMIN',(isset($_SESSION['user']) && isset($_SESSION['user']['is_admin']))?true:false);
