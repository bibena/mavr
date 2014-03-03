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
 * Abstract class which discribe functions of db driver
--------------------------------------------------------------------------*/


abstract class Pattern_Db
	{
	abstract protected function __construct();

	final private function __clone()
		{}

	final private function __wakeup()
		{}

	abstract protected function Create_Database();

	abstract protected function Create_Table(array $data);

	abstract protected function Drop_Table(array $data);

	abstract protected function Insert(array $data);

	abstract protected function Multi_Insert(array $data);

	abstract protected function Replace(array $data);

	abstract protected function Update(array $data);

	abstract protected function Select(array $data);

	abstract protected function Select_Join(array $data);

	abstract protected function Delete(array $data);

	abstract protected function Count(array $data);

	abstract protected function Insert_Id();

	abstract protected function Query($sql);
	}