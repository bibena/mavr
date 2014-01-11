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


class Product_Controller extends Pattern_Controller
	{
/*-------------------------------------------------------------------------
* Constructor of Page_Controller
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		}



/*-------------------------------------------------------------------------
* Example of Registration function
*
* User_Controller::Registration();
*
* Call functions User_Model::Registration() and User_View::Registration().
--------------------------------------------------------------------------*/
/*	function Categories()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Categories($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Category()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Category($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}*/
	function Show($id)
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Show($this->mname,array($id));
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	}