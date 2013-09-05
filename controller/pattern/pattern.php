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
* Ensister of all controllers
*
* new Pattern_Controller;
*
--------------------------------------------------------------------------*/


class Pattern_Controller
	{
/*-------------------------------------------------------------------------
* Constructor of Pattern_Controller
--------------------------------------------------------------------------*/
	function __construct()
		{
//---create name for model and view
		$mname=str_replace('Controller','Model',get_class($this));
		//$vname=str_replace('Controller','View',get_class($this));
//---and call they
		$this->model=new $mname;
		$this->view=new View;
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
			throw new Error;
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	}