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
	define('SUB_DIR',DS.$config['subdir']);
	}
else
	{
	define('SUB_DIR','');
	}