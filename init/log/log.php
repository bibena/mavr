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
 * Write data into log files
 *
 * Log::Set_Error();
--------------------------------------------------------------------------*/


class Log
	{
	private static $error_log;
	private static $info_log;



/*-------------------------------------------------------------------------
 * Install correct values to settings variable.
--------------------------------------------------------------------------*/
	private static function Init()
		{
		global $config;
		if(isset($config['infolog']))
			{
			self::$info_log=$config['infolog'];
			}
		else
			{
			self::$info_log=ROOT_DIR.DS.'log'.DS.'info.log';
			}
		if(isset($config['errorlog']))
			{
			self::$error_log=$config['errorlog'];
			}
		else
			{
			self::$error_log=ROOT_DIR.DS.'log'.DS.'error.log';
			}
		}



/*-------------------------------------------------------------------------
 * Write info data to log file
 *
 * Log::Set_Info($string);
 *
 * Return: true or false.
--------------------------------------------------------------------------*/
	public static function Set_Info($string)
		{
		self::Init();
		$string=date(DATE_RSS)."\n\t".$string."\n";
		return file_put_contents(self::$info_log,$string,FILE_APPEND);
		}



/*-------------------------------------------------------------------------
 * Write error data to log file
 *
 * Log::Set_Error($string);
 *
 * Return: true or false.
--------------------------------------------------------------------------*/
	public static function Set_Error($string)
		{
		self::Init();
		$string=date(DATE_RSS)."\n\t".$string."\n";
		return file_put_contents(self::$error_log,$string,FILE_APPEND);
		}



/*-------------------------------------------------------------------------
 * Clear info log file
 *
 * Log::Clear_Info();
 *
 * Return: true or false.
--------------------------------------------------------------------------*/
	public static function Clear_Info()
		{
		self::Init();
		return file_put_contents(self::$info_log,'');
		}



/*-------------------------------------------------------------------------
 * Clear error log file
 *
 * Log::Clear_Error();
 *
 * Return: true or false.
--------------------------------------------------------------------------*/
	public static function Clear_Error()
		{
		self::Init();
		return file_put_contents(self::$error_log,'');
		}
	}