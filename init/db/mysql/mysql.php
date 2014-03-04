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
* MySQL database driver
*
* Db::Get_Instance();
*
--------------------------------------------------------------------------*/


class Mysql_Db
	{
	private $connection;
/*-------------------------------------------------------------------------
* Constructor of MySQL database driver
--------------------------------------------------------------------------*/
	public function __construct()
		{
//---create variable with config
		$this->config=Config::Get_Instance()->Get_Config();
//---create variable with connection
		try
			{
			$this->connection=new PDO('mysql:host='.$this->config['dbhost'].';dbname='.$this->config['dbname'],$this->config['dbuser'],$this->config['dbpassword']);
			}
		catch (PDOException $e)
			{
			try
				{
				throw new Db_Error(ERROR_CONECTING_TO_DB);
				}
			catch (Db_Error $e)
				{
				$e->Error();
				}
			}
		}



/*-------------------------------------------------------------------------
* Example of Get_PDO function
*
* Mysql_Db->Get_PDO();
*
* Return: PDO class.
--------------------------------------------------------------------------*/
	public function Get_PDO()
		{
		return $this->connection;
		}
	}
