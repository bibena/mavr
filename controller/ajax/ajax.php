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
* User controller
*
* User_Controller::Registration();
*
--------------------------------------------------------------------------*/


class Ajax_Controller
	{
	function Link()
		{
		$html=new Ajax_Model;
		echo $html->Link($_POST['link']);
		}
	function Addmenuitem()
		{
		$html=new Ajax_Model;
		echo $html->Addmenuitem();
		}
	}
/*
$url=array_reverse(explode('/',$_POST['url']));
if(count($url))
	{
	$path=INIT_DIR;
	foreach($url as $dir)
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
	}*/
?>