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


class Admin_Controller extends Pattern_Controller
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
	function Main()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Main($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Config()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Config($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Menu()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Menu($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Acl()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Acl($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Users()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Users($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function User($user_id)
		{
//---call User_Model::Registration();
		$model_answer=$this->model->User($this->mname,array($user_id));
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Shop()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Shop($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Products()
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Products($this->mname);
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	function Product($product_id)
		{
//---call User_Model::Registration();
		$model_answer=$this->model->Product($this->mname,array($product_id));
//---call User_View::Registration();
		$this->view->Display($model_answer);
		}
	}