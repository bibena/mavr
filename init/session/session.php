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
		$this->Set_Agent();
		$this->Set_Request();
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
	public function Get($section='',$subsection='')
		{
		try
			{
			if($section)
				{
				if($subsection)
					{
					if(isset($_SESSION[$section][$subsection]))
						{
						$return=$_SESSION[$section][$subsection];
						}
					else
						{
						throw new Error(ERROR_SESSION_SUBSECTION);
						}
					}
				else
					{
					if(isset($_SESSION[$section]))
						{
						$return=$_SESSION[$section];
						}
					else
						{
						throw new Error(ERROR_SESSION_SECTION);
						}
					}
				}
			else
				{
				$return=$_SESSION;
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $return;
		}



/*-------------------------------------------------------------------------
 * Check session array
 *
 * Session::Check()
 *
 * Return: bool
--------------------------------------------------------------------------*/
	public function Check($section='',$subsection='')
		{
		try
			{
			if($section)
				{
				if($subsection)
					{
					if(isset($_SESSION[$section][$subsection]))
						{
						$return=true;
						}
					else
						{
						$return=false;
						}
					}
				else
					{
					if(isset($_SESSION[$section]))
						{
						$return=true;
						}
					else
						{
						$return=false;
						}
					}
				}
			else
				{
				$return=false;
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $return;
		}



/*-------------------------------------------------------------------------
 * Set session array
 *
 * Session::Set()
 *
 * Return: bool
--------------------------------------------------------------------------*/
	public function Set($value,$section,$subsection='')
		{
		try
			{
			if($value)
				{
				if($section)
					{
					if($subsection)
						{
						$_SESSION[$section][$subsection]=$value;
						}
					else
						{
						$_SESSION[$section]=$value;
						}
					}
				else
					{
					throw new Error(ERROR_SESSION_SECTION);
					}
				}
			else
				{
				throw new Error(ERROR_SESSION_VALUE);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			return false;
			}
		return true;
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
			$user_data=array('id','email','first_name','middle_name','last_name','country_id','region_id','city_id','address','phone','is_visible','is_deleted','is_admin','time_of_creation','time_of_modifying');
			foreach($user as $key=>$item)
				{
				if(in_array($key,$user_data))
					{
					$_SESSION["user"][$key]=$item;
					}
				else
					{
					throw new Error(ERROR_SESSION_USER);
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
			$_SESSION["agent"]["ip"]=$_SERVER["REMOTE_ADDR"];
			$_SESSION["agent"]["useragent"]=$_SERVER["HTTP_USER_AGENT"];
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Set sessions user_request section
 *
 * Session::Set_Request()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	private function Set_Request()
		{
		try
			{
			$_SESSION["request"]["host"]=$_SERVER["HTTP_HOST"];
			$_SESSION["request"]["uri"]=$_SERVER["REQUEST_URI"];
			$_SESSION["request"]["serverport"]=$_SERVER["SERVER_PORT"];
			$_SESSION["request"]["protocol"]=$_SERVER["SERVER_PROTOCOL"];
			$_SESSION["request"]["method"]=$_SERVER["REQUEST_METHOD"];
			$_SESSION["request"]["query"]=$_SERVER["QUERY_STRING"];
			$_SESSION["request"]["userport"]=$_SERVER["REMOTE_PORT"];
			$_SESSION["request"]["referrer"]=isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:'';
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Add or return last error
 *
 * Session::Last_Error()
 *
 * Return: last error message
--------------------------------------------------------------------------*/
	public function Last_Error($message='')
		{
		try
			{
			if($message=='')
				{
				if(isset($_SESSION["last_error"]))
					{
					$last_error=$_SESSION["last_error"];
					$this->Erase("last_error");
					return $last_error;
					}
				else
					{
					throw new Error(ERROR_SESSION_MESSAGE);
					}
				}
			else
				{
				$_SESSION["last_error"]=$message;
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Delete session or section
 *
 * Session::Erase()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public function Erase($section='',$subsection='')
		{
		try
			{
			if($section)
				{
				if($subsection)
					{
					if(isset($_SESSION[$section][$subsection]))
						{
						unset($_SESSION[$section][$subsection]);
						}
					else
						{
						throw new Error(ERROR_SESSION_SUBSECTION);
						}
					}
				else
					{
					if(isset($_SESSION[$section]))
						{
						unset($_SESSION[$section]);
						}
					else
						{
						throw new Error(ERROR_SESSION_SECTION);
						}
					}
				}
			else
				{
				session_unset();
				}
			}
		catch (Error $e)
			{
			$e->Error();
			return false;
			}
		return true;
		}
	}
