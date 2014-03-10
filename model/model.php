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


class Model
	{
	function __call($mname,$args)
		{
		try
			{
			if(isset($args[0]))
				{
				$cname=$args[0];
				}
			else
				{
				throw new Error(ERROR_MODEL_UNNOWN_CLASS);
				}
//---create example of the requested class
			$class=new $cname;
//---call requested method in class
			if(isset($args[1]))
				{
				Acl::Access($cname,$mname,$args[1]);
				return $class->$mname($args[1]);
				}
			else
				{
				Acl::Access($cname,$mname);
				return $class->$mname();
				}
			}
		catch(Error $e)
			{
			$e->Error();
			}
		}
	}