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
* Ensister of all models
*
* new Pattern_Model;
*
--------------------------------------------------------------------------*/


class Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of Pattern_Model
--------------------------------------------------------------------------*/
	function __construct()
		{
//read the name of template from config
		$config=Config::Get_Instance()->Get_Config();
		$this->dir=ROOT_DIR.DS.'template'.DS.$config['template'].DS;
//scan css and js dir of template
		$this->css=scandir($this->dir.'css');
		$this->js=scandir($this->dir.'js');
 //include all css and js file from theese dir
		$this->assets=array();
		foreach($this->js as $js)
			{
			if(!is_dir($this->dir.$js))
				{
				$this->assets[]='<script src="'.SUB_DIR.DS.'template'.DS.$config['template'].DS.'js'.DS.$js.'"></script>';
				}
			}
		foreach($this->css as $css)
			{
			if(!is_dir($this->dir.$css))
				{
				$this->assets[]='<link rel="stylesheet" href="'.SUB_DIR.DS.'template'.DS.$config['template'].DS.'css'.DS.$css.'" />';
				}
			}
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
