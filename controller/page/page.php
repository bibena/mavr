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


class Page_Controller extends Pattern_Controller
	{
/*-------------------------------------------------------------------------
* Constructor of Page_Controller
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		}
	function Show()
		{
		echo "Запущен метод ".__METHOD__;
		}



/*-------------------------------------------------------------------------
* Example of Registration function
*
* User_Controller::Registration();
*
* Call functions User_Model::Registration() and User_View::Registration().
--------------------------------------------------------------------------*/
	function Error($number='404',$error_message='')
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Error($number,$error_message);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Main()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Main();
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Logout()
		{
		echo "Запущен метод ".__METHOD__;
		}
	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}