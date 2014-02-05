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
* Helper for user models
*
* new User_Helper;
*
--------------------------------------------------------------------------*/


class User_Helper
	{
/*-------------------------------------------------------------------------
* Constructor of User_Helper
--------------------------------------------------------------------------*/
	function __construct()
		{
		}




	public function User_Login_Check($form)
		{
		try
			{
//take data from form. If form was sentbtn-success
			$db=Db::Get_Instance();
			if(!(isset($form["email"]) && filter_var($form["email"],FILTER_VALIDATE_EMAIL)))
				{
				$error["error"]=1;
				}
			else
				{
				$sql="SELECT * FROM `users` where `email` = :email;";
				$prepare=$db->prepare($sql);
				$prepare->execute(array(":email"=>$form["email"]));
				$email_exists=$prepare->fetchAll(PDO::FETCH_ASSOC);
				if(!isset($email_exists[0]))
					{
					$error["error"]=1;
					}
				else
					{
					$email_exists=$email_exists[0];
					if(isset($form["password"]) && password_verify($form["password"],$email_exists["password"]))
						{
						unset($email_exists["password"]);
						$session=new Session;
						$session->Set_User($email_exists);
						}
					else
						{
						$error["error"]=1;
						}
					}
				}
			if(!isset($error))
				{
				Redirect::Page();
				}
			else
				{
//else print the form with error message
				$return=array_merge($error,$form);
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		if(isset($return))
			{
			return $return;
			}
		}



	public function User_Registration_Save($form)
		{
		try
			{
			$db=Db::Get_Instance();
			if(!(isset($form["email"]) && filter_var($form["email"],FILTER_VALIDATE_EMAIL)))
				{
				$error["error"]["email"]=1;
				}
			else
				{
				$sql="SELECT `id` FROM `users` where `email` = :email;";
				$prepare=$db->prepare($sql);
				$prepare->execute(array(":email"=>$form["email"]));
				$email_exists=$prepare->fetchColumn();
				if($email_exists)
					{
					$error["error"]["email"]=1;
					}
				}
			if(!(isset($form["password"]) && preg_match("|^[-a-z0-9!#$%&'*+/=?^_`{\|}~]{4,}$|i",$form["password"])))
				{
				$error["error"]["password"]=1;
				}
			if(!(isset($form["agreement"]) && $form["agreement"]))
				{
				$error["error"]["agreement"]=1;
				}
			if(!(isset($form["name"]) && $form["name"]))
				{
				$error["error"]["name"]=1;
				}
			if(isset($error))
				{
//else print the form with error message
				$error["data"]=$form;
				$return=$error;
				}
			else
				{
//check data from this form
				$sql="INSERT INTO `users` (`email`,`password`,`first_name`,`last_name`,`country_id`,`city_id`) VALUES (:email,:password,:first_name,:last_name,:country_id,:city_id);";
				$form_data=array(":email"=>$form["email"],
								":password"=>password_hash($form["password"],PASSWORD_BCRYPT,array("cost"=>9)),
								":first_name"=>$form["name"],
								":last_name"=>'',
								":country_id"=>'1',
								":city_id"=>'1');
				$prepare=$db->prepare($sql);
				$prepare->execute($form_data);
				$new_user_id=$db->lastInsertId();
				$return=$new_user_id;
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $return;
		}


/*-------------------------------------------------------------------------
* Example of Check method
*
* User_Helper::Check(array('pass'=>array('password','string_for_check')));
*
* Return: array with bool result for every element.
--------------------------------------------------------------------------*/
/*	function Check(array $userdata)
		{
		$result=array();
		foreach($userdata as $key=>$data)
			{
			switch($data[0])
				{
				case 'string':
					$result[$key]=is_string($data[1]);
					break;
				case 'numeric':
					$result[$key]=is_numeric($data[1]);
					break;
				case 'email':
					$result[$key]=$this->Email_Check($data[1]);
					break;
				case 'password':
					$result[$key]=$this->Password_Check($data[1]);
					break;
				default:
					break;
				}
			}
		return $result;
		}



/*-------------------------------------------------------------------------
* Example of Password_Check method
*
* User_Helper::Password_Check('p@ssword');
*
* Return: bool result for password.
--------------------------------------------------------------------------*/
/*	function Password_Check($password)
		{
		return preg_match("|^[-a-z0-9!#$%&'*+/=?^_`{\|}~]{6,}$|i",$password);
		}



/*-------------------------------------------------------------------------
* Example of Email_Check method
*
* User_Helper::Email_Check('email@check.me');
*
* Return: bool result for email.
--------------------------------------------------------------------------*/
/*	function Email_Check($email)
		{
		$return=true;
		if(filter_var($email, FILTER_VALIDATE_EMAIL)===false)
			{
			$return=false;
			}
		return $return;
		}



	public function Select_User($id)
		{
		try
			{
			$prepare=Db::Get_Instance()->prepare("SELECT * FROM `users` where id = :id");
			$prepare->execute(array(':id'=>$id));
			}
		catch (PDOException $e)
			{
			die("Error!: ".$e->getMessage()."<br/>");
			}
		return $prepare->fetchAll(PDO::FETCH_ASSOC);
		}

	public function Login_Exists($email)
		{
		try
			{
			$prepare=Db::Get_Instance()->prepare("SELECT * FROM `users` where `email` = :email");
			$prepare->execute(array(':email'=>$email));
			}
		catch (PDOException $e)
			{
			die("Error!: ".$e->getMessage()."<br/>");
			}
		$answer=$prepare->fetchAll(PDO::FETCH_ASSOC);
		if(count($answer)!=1)
			{
			return false;
			}
		else
			{
			return $answer[0];
			}
		}

	public function Create_User($params)
		{
		try
			{
			$prepare=Db::Get_Instance()->prepare("INSERT INTO `users` (`email`,`password`,`lastname`,`firstname`,`phone`) VALUES (:email,:password,:lastname,:firstname,:phone);");
			$prepare->execute($params);
			}
		catch (PDOException $e)
			{
			die("Error!: ".$e->getMessage()."<br/>");
			}
		//return $prepare->fetchAll(PDO::FETCH_ASSOC);
		}*/
	}
