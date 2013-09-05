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
 * Define class Error which extends Exception
--------------------------------------------------------------------------*/


class Db_Error extends Error
	{
/*-------------------------------------------------------------------------
* Constructor of Error class
--------------------------------------------------------------------------*/


	public function __construct($message='', $code = 0, Exception $previous = null) 
		{
		parent::__construct($message, $code, $previous);
		}
	}
