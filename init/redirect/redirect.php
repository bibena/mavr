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
 * Redirect to some page
 *
 * Redirect::Page();
--------------------------------------------------------------------------*/


class Redirect
	{
	private function __construct()
		{}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Redirect to some page
 *
 * Redirect::Link()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public static function Link($link)
		{
		try
			{
			$link=trim($link," \n\r\t\0\x0B/");
			if(filter_var($link,FILTER_VALIDATE_URL)!==false)
				{
				$redirect=$link;
				}
			else
				{
				throw new Error('Wrong parameter path');
				}
			header('Location: '.$redirect);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
 * Redirect to some page
 *
 * Redirect::Page()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public static function Page($path)
		{
		try
			{
			$config=Config::Get_Instance()->Get_Config();
			$path=trim($path," \n\r\t\0\x0B/");
			if($path==='')
				{
				$redirect=$_SERVER['HTTP_HOST'].$config['subdir'];
				}
			elseif($path==='.')
				{
				$redirect=$_SERVER['HTTP_HOST'].DS.$_SERVER['REQUEST_URI'];
				}
			else
				{
				$patharray=explode(DS,$path);
				if(count($patharray)==2 || count($patharray)==3)
					{
					if(file_exists(ROOT_DIR.DS.'controller'.DS.$patharray[0].DS.$patharray[0].'.php'))
						{
						$redirect=$_SERVER['HTTP_HOST'].$config['subdir'].DS.implode(DS,$patharray);
						}
					else
						{
						throw new Error('Wrong parameter path');
						}
					}
				else
					{
					throw new Error('Wrong parameter path');
					}
				}
			header('Location: http://'.$redirect);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}
	}
