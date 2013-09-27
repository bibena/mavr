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
			$content['assets']=implode("\n",$this->assets)."\n";
//if form was sent from this site
			if(!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])!==false)
				{
//take data from form. If form was sent
				if(isset($_POST['flag']) && $_POST['flag'])
					{
					if(!(isset($_POST['email']) && $_POST['email'] && $this->helper->Email_Check($_POST['email'])))
						{
						$error['error']['email']=1;
						}
					if(!(isset($_POST['password']) && $this->helper->Password_Check($_POST['password'])))
						{
						$error['error']['password']=1;
						}
					if(!(isset($_POST['agreement']) && $_POST['agreement']))
						{
						$error['error']['agreement']=1;
						}
					if(!(isset($_POST['name']) && $_POST['name']))
						{
						$error['error']['name']=1;
						}
					if($this->helper->Login_Exists($_POST['email']))
						{
						$error['error']['email']=1;
						}
					if(!isset($error))
						{
//check data from this form
						$this->helper->Create_User(array(
												':email'=>$_POST['email'],
												':password'=>password_hash($_POST['password'],PASSWORD_BCRYPT,array('cost'=>9)),
												':lastname'=>NULL,
												':firstname'=>$_POST['name'],
												':phone'=>NULL,
												':timeofregistration'=>time()
												));
						$id_user=Db::Get_Instance()->lastInsertId();
						$content['content']='All right! User was added with id = '.$id_user;
						}
					else
						{
//else print the form with error message
						$error['data']=$_POST;
						$content['content']=$this->view->Content_Create(__METHOD__,$error);
						}
					}
				else
					{
//else print the form
					$content['content']=$this->view->Content_Create(__METHOD__,array());
					}
				}
			else
				{
//else throw an exception
				throw new Error('Wrong URL. Form wasn`t sent from this site.');
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		//$content['content']=$this->view->Registration($error);
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
			$content['assets']=implode("\n",$this->assets)."\n";
//if form was sent from this site
			if(!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])!==false)
				{
//take data from form. If form was sent
				if(isset($_POST['flag']) && $_POST['flag'])
					{
					if(!(isset($_POST['password']) && $this->helper->Password_Check($_POST['password'])))
						{
						$error['error']=1;
						}
					if(!(isset($_POST['email']) && $_POST['email'] && $this->helper->Email_Check($_POST['email'])))
						{
						$error['error']=1;
						}
					else
						{
						$user_data=$this->helper->Login_Exists($_POST['email']);
						if($user_data)
							{
							if(password_verify($_POST['password'],$user_data['password']))
								{
								unset($user_data['password']);
								$this->session->Set_User($user_data);
								}
							else
								{
								$error['error']=1;
								}
							}
						else
							{
							$error['error']=1;
							}
						}

					if(!isset($error))
						{
						$content['content']='All right! User was loged in';
						}
					else
						{
//else print the form with error message
						$error= array_merge($error,$_POST);
						$content['content']=$this->view->Content_Create(__METHOD__,$error);
						}
					}
				else
					{
//else print the form
					$content['content']=$this->view->Content_Create(__METHOD__,array());
					}
				}
			else
				{
//else throw an exception
				throw new Error('Wrong URL. Form wasn`t sent from this site.');
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
		$this->session->Erase('user');
		Redirect::Page();
		}
	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}
