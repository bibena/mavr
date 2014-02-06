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
* User model
*
* User_Model::Registration();
*
--------------------------------------------------------------------------*/


class User_Model extends Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of User_Model
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		$this->view=new View;
		require_once('password.php');
		require_once('helper.php');
		$this->helper=new User_Helper;
		$this->form=parent::Check();
		}



	function Show()
		{
		echo "Запущен метод ".__METHOD__;
		}



/*-------------------------------------------------------------------------
* Example of Registration function
*
* User_Model::Registration();
*
* Return content for View.
--------------------------------------------------------------------------*/
	function Registration()
		{
		try
			{
//include css and js
			$content["assets"]=implode("\n",$this->assets)."\n";
//if was sent form
			if(count($this->form)>0)
				{
				$is_user_added=$this->helper->User_Registration_Save($this->form);
				if(is_array($is_user_added))
					{
					$content["content"]=$this->view->Content_Create(__METHOD__,$is_user_added);
					}
				else
					{
					$content["content"]='User was added with id = '.$is_user_added;
					}
				}
			else
				{
//else print the form
				$content["content"]=$this->view->Content_Create(__METHOD__,array());
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}



/*-------------------------------------------------------------------------
* Example of Login function
*
* User_Model::Login();
*
* Return content for View.
--------------------------------------------------------------------------*/
	function Login()
		{
		try
			{
//include css and js
			$content["assets"]=implode("\n",$this->assets)."\n";
//if was sent form
			if(count($this->form)>0)
				{
				$is_user_login=$this->helper->User_Login_Check($this->form);
				if(is_array($is_user_login))
					{
					$content["content"]=$this->view->Content_Create(__METHOD__,$is_user_login);
					}				
				}
			else
				{
//else print the form
				$content["content"]=$this->view->Content_Create(__METHOD__,array());
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}



	function Logout()
		{
		try
		{
		global $session;
		if($session->Check("user"))
			{
			$session->Erase();
			}
		Redirect::Page('^');		
		}
		catch (Error $e)
			{
			$e->Error();
			}
		return true;
		}



	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}
