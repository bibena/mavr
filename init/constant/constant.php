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
//define('SUB_DIR',$config['subdir']!==''?DS.$config['subdir'].DS:preg_match('@^/mavr/@',$_SERVER["REQUEST_URI"])?DS.'mavr'.DS:DS);
if($config['subdir']!=='')
	{
	define('SUB_DIR',DS.$config['subdir'].DS);
	}
else
	{
	if(preg_match('@^/mavr/@',$_SERVER["REQUEST_URI"]))
		{
		define('SUB_DIR',DS.'mavr'.DS);
		}
	else
		{
		define('SUB_DIR',DS);
		}
	}

define('IS_LOGIN',isset($_SESSION['user'])?true:false);