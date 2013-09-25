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
* Ancestor of all views
*
* new Pattern_View;
*
--------------------------------------------------------------------------*/


class View
	{
/*-------------------------------------------------------------------------
* Constructor of View
--------------------------------------------------------------------------*/
	function __construct()
		{
//create address of pattern
		$config=Config::Get_Instance()->Get_Config();
		$this->template=ROOT_DIR.DS.'template'.DS.$config['template'].DS.$config['template'].'.html';
		}



/*-------------------------------------------------------------------------
* Example of Content_Create method
*
* View::Content_Create($path,$content);
*
* Create content for displaying on the screen.
--------------------------------------------------------------------------*/
	function Content_Create($method,$content)
		{
		list($folder,$file)=str_replace('_Model','',explode('::',$method));
//create content
		ob_start();
		include_once(strtolower($folder).DS.strtolower($file).".html");
		$return=ob_get_contents();
		ob_end_clean();
		return $return;
		/*
		ob_start();
		include_once($this->template);
		ob_end_flush();*/
		}



/*-------------------------------------------------------------------------
* Example of Display method
*
* Class::Display();
*
* Display content in the screen.
--------------------------------------------------------------------------*/
	function Display($content)
		{
//output content to the browser
		ob_start();
		include_once($this->template);
		ob_end_flush();
		}



/*-------------------------------------------------------------------------
* Example of __call method
*
* Class::Unknown_Method();
*
* Return: throw an exception.
--------------------------------------------------------------------------*/
	function __call($name, $arguments)
		{
//---if were calling unknown method throw an exception
		try
			{
			throw new Error('Wrong URL. Error calling unknow method '.$name);
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	}