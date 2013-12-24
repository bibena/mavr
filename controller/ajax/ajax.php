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


class Ajax_Controller extends Pattern_Controller
	{
	function Link()
		{		
		echo $this->model->Link($this->mname,array($_POST['link']));
		}
	}
?>