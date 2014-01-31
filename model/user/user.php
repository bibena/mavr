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
			$content['assets']=implode("\n",$this->assets)."\n";
//if was sent form
			if(count($this->form)>0)
				{
				if(!(isset($this->form['email']) && $this->form['email'] && $this->helper->Email_Check($this->form['email'])))
					{
					$error['error']['email']=1;
					}
				if(!(isset($this->form['password']) && $this->helper->Password_Check($this->form['password'])))
					{
					$error['error']['password']=1;
					}
				if(!(isset($this->form['agreement']) && $this->form['agreement']))
					{
					$error['error']['agreement']=1;
					}
				if(!(isset($this->form['name']) && $this->form['name']))
					{
					$error['error']['name']=1;
					}
				if($this->helper->Login_Exists($this->form['email']))
					{
					$error['error']['email']=1;
					}
				if(!isset($error))
					{
//check data from this form
					$this->helper->Create_User(array(
											':email'=>$this->form['email'],
											':password'=>password_hash($this->form['password'],PASSWORD_BCRYPT,array('cost'=>9)),
											':lastname'=>NULL,
											':firstname'=>$this->form['name'],
											':phone'=>NULL
											));
					$id_user=Db::Get_Instance()->lastInsertId();
					$content['content']='All right! User was added with id = '.$id_user;
					}
				else
					{
//else print the form with error message
					$error['data']=$this->form;
					$content['content']=$this->view->Content_Create(__METHOD__,$error);
					}
				}
			else
				{
//else print the form
				$content['content']=$this->view->Content_Create(__METHOD__,array());
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
//if was sent form
			if(count($this->form)>0)
				{
//take data from form. If form was sentbtn-success
				if(!(isset($this->form['password']) && $this->helper->Password_Check($this->form['password'])))
					{
					$error['error']=1;
					}
				if(!(isset($this->form['email']) && $this->form['email'] && $this->helper->Email_Check($this->form['email'])))
					{
					$error['error']=1;
					}
				else
					{
					$user_data=$this->helper->Login_Exists($this->form['email']);
					if($user_data)
						{
						if(password_verify($this->form['password'],$user_data['password']))
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
					//$content['content']='All right! User was loged in';
					Redirect::Page();
					}
				else
					{
	//else print the form with error message
					$error= array_merge($error,$this->form);
					$content['content']=$this->view->Content_Create(__METHOD__,$error);
					}
				}
			else
				{
//else print the form
				$content['content']=$this->view->Content_Create(__METHOD__,array());
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
