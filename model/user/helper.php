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



/*-------------------------------------------------------------------------
* Example of Check method
*
* User_Helper::Check(array('pass'=>array('password','string_for_check')));
*
* Return: array with bool result for every element.
--------------------------------------------------------------------------*/
	function Check(array $userdata)
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
	function Password_Check($password)
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
	function Email_Check($email)
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
			$prepare=Db::Get_Instance()->prepare("SELECT `password` FROM `users` where `email` = :email");
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
			return $answer[0]['password'];
			}
		}

	public function Create_User($params)
		{
		try
			{
			$prepare=Db::Get_Instance()->prepare("INSERT INTO `users` (`email`,`password`,`age`,`fname`,`lname`,`cdate`) VALUES (:email,:password,:age,:fname,:lname,:cdate);");
			$prepare->execute($params);
			}
		catch (PDOException $e)
			{
			die("Error!: ".$e->getMessage()."<br/>");
			}
		return $prepare->fetchAll(PDO::FETCH_ASSOC);
		}
	}
