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


class User_Controller extends Pattern_Controller
	{
/*-------------------------------------------------------------------------
* Constructor of User_Controller
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
	function Registration()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Registration($this->mname);
//---call View::Display();
		$this->view->Display($model_answer);
		}
	function Login()
		{
//---call User_Model::Login();
		$model_answer=$this->model->Login($this->mname);
//---call View::Display();
		$this->view->Display($model_answer);
		}
	function Logout()
		{
//---call User_Model::Logout();
		$model_answer=$this->model->Logout($this->mname);
		}
	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}