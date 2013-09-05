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


class Model
	{
	function __construct()
		{
/*		if(isset($_GET['controller']) && isset($_GET['method']) && preg_match("/^[a-z]+$/",$_GET['controller']) && preg_match("/^[a-z_]+$/",$_GET['method']))
			{
			$controller=$_GET['controller'];
			$method=$_GET['method'];
			}
		else
			{
			$controller='page';
			$method='main';
			}
		$cname=ucfirst($controller).'_Controller';
		$mname=implode('_',array_map('ucfirst',explode('_',$method)));
		$class=new $cname;
		$class->$mname();*/
		}
	
	}