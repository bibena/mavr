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
 * Include lang file
 *
 * Lang::Set_Lang();
--------------------------------------------------------------------------*/


class Lang
	{
/*-------------------------------------------------------------------------
 * Constructor of Lang class
 *
 * Lang();
 *
 * Return: include lang file.
--------------------------------------------------------------------------*/
	function __construct()
		{
		$config=Config::Get_Instance()->Get_Config();
		$this->Set_Lang($config['lang']);
		}



/*-------------------------------------------------------------------------
 * Return current lang
 *
 * Lang::Get_Lang();
 *
 * Return: string name of current lang.
--------------------------------------------------------------------------*/
	public static function Get_Lang()
		{
		return $this->config['lang'];
		}



/*-------------------------------------------------------------------------
 * Set lang
 *
 * Lang::Set_Lang();
 *
--------------------------------------------------------------------------*/
	public function Set_Lang($lang)
		{
		$dirname=ROOT_DIR.DS.'lang'.DS.strtolower($lang);
		if(file_exists($dirname))
			{
			$lang_dir=scandir($dirname);
			foreach($lang_dir as $file)
				{
				if(!is_dir($file))
					{
					include_once($dirname.DS.$file);
					}
				}
			}
		}
	}
