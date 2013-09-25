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
 * Keeping and managing session
 *
 * Session::Get();
--------------------------------------------------------------------------*/


class Session
	{
	public function __construct()
		{
		session_cache_expire(1440);
		$this->Set_Agent();
		}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Get session array
 *
 * Session::Get()
 *
 * Return: array
--------------------------------------------------------------------------*/
	public function Get($section='')
		{
		try
			{
			if($section=='')
				{
				$return=$_SESSION;
				}
			else
				{
				if(isset($_SESSION[$section]))
					{
					$return=$_SESSION[$section];
					}
				else
					{
					throw new Error('Wrong session section requested');
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $return;
		}



/*-------------------------------------------------------------------------
 * Set sessions user section
 *
 * Session::Set_User()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public function Set_User(array $user)
		{
		try
			{
			$user_data=array('id','isadmin','email','firstname','lastname','phone','country','region','city','adress','timeoflogin','timeofregistration');
			foreach($user as $key=>$item)
				{
				if(in_array($key,$user_data))
					{
					$_SESSION['user'][$key]=$item;
					}
				else
					{
					throw new Error('trying to set wrong user section to session');
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Set sessions user_agent section
 *
 * Session::Set_Agent()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	private function Set_Agent()
		{
		try
			{
			$_SESSION['agent']['ip']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['agent']['useragent']=$_SERVER['HTTP_USER_AGENT'];
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Delete session or section
 *
 * Session::Unset()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public function Erase($section='')
		{
		try
			{
			if($section=='')
				{				
				session_unset($_SESSION['user']);
				}
			else
				{
				if(isset($_SESSION[$section]))
					{
					unset($_SESSION[$section]);
					}
				else
					{
					throw new Error('Wrong session section requested');
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}
	}
