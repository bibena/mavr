<?php

/*-------------------------------------------------------------------------
 * Define class Flag_Error which extends Error
--------------------------------------------------------------------------*/


class Flag_Error extends Error
	{
/*-------------------------------------------------------------------------
* Constructor of Flag_Error class
--------------------------------------------------------------------------*/
	public function __construct($message='', $code = 0, Exception $previous = null) 
		{
		parent::__construct($message, $code, $previous);
		}
	}
