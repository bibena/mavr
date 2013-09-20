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
												':password'=>md5($_POST['password']),
												':fname'=>$_POST['name'],
												':lname'=>NULL,
												':age'=>NULL,
												':cdate'=>time()
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
						$password_hash=$this->helper->Login_Exists($_POST['email']);
						if($password_hash)
							{
							if($password_hash==md5($_POST['password']))
								{
								//do smthg
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
		echo "Запущен метод ".__METHOD__;
		}
	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}
