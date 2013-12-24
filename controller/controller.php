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
* Ancestor of all controllers
*
* new Controller;
*
--------------------------------------------------------------------------*/


class Controller
	{
/*-------------------------------------------------------------------------
* Constructor of Controller
--------------------------------------------------------------------------*/
	function __construct()
		{
		try
			{
	//---check URL and if some controller and method were requerted keep they in memory
			if(isset($_GET['controller']) && isset($_GET['method']) && preg_match("/^[a-z]+$/",$_GET['controller']) && preg_match("/^[a-z_]+$/",$_GET['method']))
				{
				$controller=$_GET['controller'];
				$method=$_GET['method'];
				if(isset($_GET['argument']) && preg_match("/^[0-9]+$/",$_GET['argument']))
					{
					$argument=$_GET['argument'];
					}
				}
	//---else show main page
			else
				{
				$controller='page';
				$method='main';
				}
	//---processing the request
			$cname=ucfirst($controller).'_Controller';
			$mname=implode('_',array_map('ucfirst',explode('_',$method)));
	//---create example of the requested class
			$class=new $cname;
	//---call requested method in class
			if(isset($argument))
				{
				$class->$mname($argument);
				}
			else
				{
				$class->$mname();
				}
			}
		catch(Error $e)
			{
			$e->Error();
			}
		}
	}