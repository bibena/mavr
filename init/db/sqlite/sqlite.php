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
* SQLite-PDO database driver
*
* Db::Get_Instance();
*
--------------------------------------------------------------------------*/


class Sqlite_Db
	{
	private $connection;
/*-------------------------------------------------------------------------
* Constructor of SQLite database driver
--------------------------------------------------------------------------*/
	public function __construct()
		{
//---create variable with config
		$this->config=Config::Get_Instance()->Get_Config();
//---create variable with connection
		try
			{
			$this->connection=new PDO('sqlite:'.$this->config['dbpath']);
//---use foreign keys for table
			$this->connection->exec('PRAGMA foreign_keys = 1;');
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
* Sqlite_Db->Get_PDO();
*
* Return: PDO class.
--------------------------------------------------------------------------*/
	public function Get_PDO()
		{
		return $this->connection;
		}





















/*
	public function Create_Database()
		{
		try
			{
//---check or create database
			$this->connection=new SQLite3($this->config['dbpath']);
			if(!is_a($this->connection,'SQLite3'))
				{
				throw new Db_Error('Error connecting with database');
				}
			}
//---if any error in connection catch it
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---return answer from database (true)
		return true;
		}



/*-------------------------------------------------------------------------
* Example of Create_Table function
*
* $db->Create_Table(array(
*							'tablename'=>'tablename',
*							'temporary',
* 							'if_not_exists',
* 							'columns'=>array(
*											array('name'=>'id','type'=>'int','ai'),
*											array('name'=>'a','type'=>'int','default'=>'10','not_null'),
*											array('name'=>'b','type'=>'int','not_null')
* 											),
* 							'primary_key'=>array('id'),
* 							'unique'=>array('a','b'),
* 							'foreign_key'=>array(
* 												array('key','table','key','on_update'=>'cascade','on_delete'=>'restrict'),
* 												array('key','table','key')
* 												)
* 							['foreign_key'=>array('key','table','key','on_update'=>'cascade','on_delete'=>'restrict')]
* 							));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Create_Table(array $data)
		{
		try
			{
//---CREATE claude
			$sql='CREATE';
//---TEMPORARY claude
			if(in_array('temporary',$data))
				{
				$sql.=' TEMPORARY';
				}
//---TABLE claude
			$sql.=' TABLE';
//---IF NOT EXISTS claude
			if(in_array('if_not_exists',$data))
				{
				$sql.=' IF NOT EXISTS';
				}
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.=' `'.$this->connection->escapeString($data['tablename']).'`';
				}
			else
				{
				throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---colunn claude
			$sql.=' (';
			if(isset($data['columns']) && is_array($data['columns']))
				{
				$types=array('integer','real','blob','text','null');
				foreach($data['columns'] as $cname=>$cval)
					{
					if(in_array('ai',$cval))
						{
						$ai[]=$cname;
						}
					}
				if(isset($ai) && count($ai)>1)
					{
					throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Set more then one autoincrement');
					}
				foreach($data['columns'] as $column)
					{
					if(isset($column['name']))
						{
						$sql.="`".$this->connection->escapeString($column['name'])."`";
						}
					else
						{
						throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set column name');
						}
					if(isset($column['type']) && in_array($column['type'],$types))
						{
						$sql.=' '.strtoupper($column['type']);
						}
					if(in_array('ai',$column))
						{
						if(!isset($column['type']))
							{
							$sql.=' INTEGER PRIMARY KEY AUTOINCREMENT';
							}
						elseif($column['type']=='integer')
							{
							$sql.=' PRIMARY KEY AUTOINCREMENT';
							}
						else
							{
							throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wrong column type for autoincrement.');
							}
						}
					if(in_array('not_null',$column))
						{
						$sql.=' NOT NULL';
						}
					if(!in_array('ai',$column) && isset($column['default']))
						{
						$sql.=" DEFAULT '".$this->connection->escapeString($column['default'])."'";
						}
					$sql.=',';
					}
				}
			else
				{
				throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Weren`t set columns or they weren`t in array');
				}
//---PRIMARY KEY claude
			if(isset($data['primary_key']))
				{
				if(!in_array($ai[0],$data['primary_key']))
					{
					$sql.=" PRIMARY KEY ('".implode("`,`",$data['primary_key'])."`),";
					}
				else
					{
					if(count($data['primary_key'])>1)
						{
						foreach($data['primary_key'] as $primary)
							{
							$data['unique'][]=$primary;
							}
						}
					}
				}
//---UNIQUE claude
			if(isset($data['unique']))
				{
				$sql.=" UNIQUE (`".implode("`,`",array_unique($data['unique']))."`),";
				}
//---FOREIGN KEY claude
			$fkval=array('cascade','restrict');
			if(isset($data['foreign_key']))
				{
				if(is_array($data['foreign_key'][0]))
					{
					foreach($data['foreign_key'] as $fkey)
						{
						$sql.=' FOREIGN KEY ('.$this->connection->escapeString($fkey[0]).') REFERENCES '.$this->connection->escapeString($fkey[1]).'('.$this->connection->escapeString($fkey[2]).')';
						if(isset($fkey['on_update']) && in_array($fkey['on_update'],$fkval))
							{
							$sql.=' ON UPDATE '.$this->connection->escapeString($fkey['on_update']);
							}
						if(isset($fkey['on_delete']) && in_array($fkey['on_delete'],$fkval))
							{
							$sql.=' ON DELETE '.$this->connection->escapeString($fkey['on_delete']);
							}
						$sql.=',';
						}
					}
				else
					{
					$sql.=' FOREIGN KEY ('.$this->connection->escapeString($data['foreign_key'][0]).') REFERENCES '.$this->connection->escapeString($data['foreign_key'][1]).'('.$this->connection->escapeString($data['foreign_key'][2]).')';
					if(isset($data['foreign_key']['on_update']) && in_array($data['foreign_key']['on_update'],$fkval))
						{
						$sql.=' ON UPDATE '.strtoupper($data['foreign_key']['on_update']);
						}
					if(isset($data['foreign_key']['on_delete']) && in_array($data['foreign_key']['on_delete'],$fkval))
						{
						$sql.=' ON DELETE '.strtoupper($data['foreign_key']['on_delete']);
						}
					}
				}
			$sql=preg_replace('/\,$/','',$sql);
			$sql.=');';
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return true
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Drop_Table function
*
* $db->Drop_Table(array(
*						'tablename'=>'tablename',
*						'if_exists'
*						);
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Drop_Table(array $data)
		{
		try
			{
//---DROP claude
			$sql='DROP';
//---TABLE claude
			$sql.=' TABLE';
//---IF EXISTS claude
			if(in_array('if_exists',$data))
				{
				$sql.=' IF EXISTS';
				}
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.=' `'.$this->connection->escapeString($data['tablename']).'`;';
				}
			else
				{
				throw new Db_Error('Error deleting the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error deleting the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return true
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Insert function
*
* $db->Insert(array(
*					'tablename'=>'tablename',
*					'ignore',
* 					'set'=>array('title1'=>'set1','title2'=>'set2')
* 					));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Insert(array $data)
		{
		try
			{
//---INSERT claude
			$sql='INSERT';
//---IGNORE claude
			if(in_array('ignore',$data))
				{
				$sql.=' OR IGNORE';
				}
//---INTO claude
			$sql.=' INTO';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.=' `'.$this->connection->escapeString($data['tablename']).'`';
				}
			else
				{
				throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---SET claude
			if(isset($data['set']) && is_array($data['set']))
				{
				$sql.=' (';
				foreach(array_keys($data['set']) as $title)
					{
					$sql.="`".$this->connection->escapeString($title)."`,";
					}
				$sql=preg_replace('/\,$/',')',$sql);
				$sql.=' VALUES (';
				foreach($data['set'] as $set)
					{
					$sql.="'".$this->connection->escapeString($set)."',";
					}
				$sql=preg_replace('/\,$/',')',$sql);
				$sql.=';';
				}
			else
				{
				throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set set part or it wasn`t array');
				}
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Multi_Insert function
*
* $db->Multi_Insert(array(
* *							'tablename'=>'tablename',
* 							'set'=>array(
* 										array(
*											'ignore',
* 											'set'=>array('title1'=>'set1','title2'=>'set2')
* 											),
* 										array(
*											'ignore',
* 											'set'=>array('title1'=>'set1','title2'=>'set2')
* 											)
* 							)));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Multi_Insert(array $data)
		{
		try
			{
//---start transaction
			$this->Query('BEGIN TRANSACTION;');
			if(isset($data['set']) && is_array($data['set']))
				{
				foreach($data['set'] as $query)
					{
//---INSERT claude
					$sql='INSERT';
//---IGNORE claude
					if(in_array('ignore',$query))
						{
						$sql.=' IGNORE';
						}
//---INTO claude
					$sql.=' INTO';
//---tablename claude
					if(isset($data['tablename']))
						{
						$sql.=' `'.$this->connection->escapeString($data['tablename']).'`';
						}
					else
						{
						throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
						}
//---SET claude
					if(isset($query['set']) && is_array($query['set']))
						{
						$sql.=' (';
						foreach(array_keys($query['set']) as $title)
							{
							$sql.="`".$this->connection->escapeString($title)."`,";
							}
						$sql=preg_replace('/\,$/',')',$sql);
						$sql.=' VALUES (';
						foreach($query['set'] as $set)
							{
							$sql.="'".$this->connection->escapeString($set)."',";
							}
						$sql=preg_replace('/\,$/',')',$sql);
						$sql.=';';
						}
					else
						{
						throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set set part or it wasn`t array');
						}
//---send query
					if(!in_array('query',$data))
						{
						$result=$this->connection->query($sql);
						if (!is_a($result,'SQLite3Result'))
							{
							throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
							}
						}
					@$count_last++;
					}
				}
			else
				{
				throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set set part or it wasn`t array');
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$this->Query('ROLLBACK;');
			$e->Error();
			}
//---else commit transaction and return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			$this->Query('COMMIT;');
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Replace function
*
* $db->Replace(array(
*						'tablename'=>'tablename',
* 						'set'=>array('title1'=>'set1','title2'=>'set2')
* 						));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Replace(array $data)
		{
		try
			{
//---INSERT claude
			$sql='INSERT OR REPLACE';
//---INTO claude
			$sql.=' INTO';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.=' `'.$this->connection->escapeString($data['tablename']).'`';
				}
			else
				{
				throw new Db_Error('Error replacing data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---SET claude
			if(isset($data['set']) && is_array($data['set']))
				{
				$sql.=' (';
				foreach(array_keys($data['set']) as $title)
					{
					$sql.="`".$this->connection->escapeString($title)."`,";
					}
				$sql=preg_replace('/\,$/',')',$sql);
				$sql.=' VALUES (';
				foreach($data['set'] as $set)
					{
					$sql.="'".$this->connection->escapeString($set)."',";
					}
				$sql=preg_replace('/\,$/',')',$sql);
				$sql.=';';
				}
			else
				{
				throw new Db_Error('Error replacing data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set set part or it wasn`t array');
				}
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error replacing data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Update function
*
* $db->Update(array(
*					'tablename'=>'tablename',
*					'ignore',
* 					'set'=>array('title1'=>'set1','title2'=>'set2'),
* 					'where'=>array(
*									array('field1','=','value1',1),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'limit'=>array('value1','from')
* 					['limit'=>'value1']
* 					));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Update(array $data)
		{
		try
			{
//---UPDATE claude
			$sql='UPDATE ';
//---IGNORE claude
			if(in_array('ignore',$data))
				{
				$sql.='OR IGNORE ';
				}
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.='`'.$this->connection->escapeString($data['tablename']).'` ';
				}
			else
				{
				throw new Db_Error('Error updating data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---SET claude
			if(isset($data['set']) && is_array($data['set']))
				{
				$sql.='SET';
				foreach($data['set'] as $title=>$set)
					{
					$sql.=" `".$this->connection->escapeString($title)."`='".$this->connection->escapeString($set)."',";
					}
				$sql=preg_replace('/\,$/',' ',$sql);
				}
			else
				{
				throw new Db_Error('Error updating data in the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set set part or it wasn`t array');
				}
//---WHERE claude
			if(isset($data['where']) && is_array($data['where']))
				{
				$sql.='WHERE (';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$dir='AND';
						if(isset($where[3]) && $where[3]===1)
							{
							$dir='OR';
							}
						switch($where[1])
							{
							case 'in':
								$sql.=" `".$this->connection->escapeString($where[0])."` IN ('".implode("','",$where[2])."') $dir";
								break;
							case 'not_in':
								$sql.=" `".$this->connection->escapeString($where[0])."` NOT IN ('".implode("','",$where[2])."') $dir";
								break;
							case '=':
							case '!=':
							case '>=':
							case '<=':
							case '<':
							case '>':
								$sql.=" `".$this->connection->escapeString($where[0])."`".$this->connection->escapeString($where[1])."'".$this->connection->escapeString($where[2])."' $dir";
								break;
							}
						}
					$sql=preg_replace("/{$dir}$/",'',$sql);
					}
				else
					{
					switch($data['where'][1])
						{
						case 'in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` IN ('".implode("','",$data['where'][2])."')";
							break;
						case 'not_in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` NOT IN ('".implode("','",$data['where'][2])."')";
							break;
						case '=':
						case '!=':
						case '>=':
						case '<=':
						case '<':
						case '>':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`".$this->connection->escapeString($data['where'][1])."'".$this->connection->escapeString($data['where'][2])."' ";
							break;
						}
					
					}
				$sql.=')';
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
//---if any error - throw exception
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error updating data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Select function
*
* $db->Select(array(
*					'tablename'=>'tablename',
*					'distinct',
* 					'fields'=>array('field1','field2'),
* 					'where'=>array(
*									array('field1','=','value1',1),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'group_by'=>'field',
* 					'order_by'=>'field',
* 					['order_by'=>array('field',1),]
* 					'limit'=>array('value1','from')
* 					['limit'=>'value1']
* 					));
*
* Return: assoc array of result or NULL or throw an exception if error.
--------------------------------------------------------------------------
	public function Select(array $data)
		{
		try
			{
//---SELECT claude
			$sql='SELECT ';
//---DISTINCT claude
			if(in_array('distinct',$data))
				{
				$sql.='DISTINCT ';
				}
//---fields claude
			if(isset($data['fields']) && is_array($data['fields']))
				{
				$sql.='`'.implode('`,`',$data['fields']).'`';
				}
			else
				{
				$sql.='*';
				}
//---FROM claude
			$sql.='FROM ';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.='`'.$this->connection->escapeString($data['tablename']).'` ';
				}
			else
				{
				throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---WHERE claude
			if(isset($data['where']) && is_array($data['where']))
				{
				$sql.='WHERE (';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$dir='AND';
						if(isset($where[3]) && $where[3]===1)
							{
							$dir='OR';
							}
						switch($where[1])
							{
							case 'in':
								$sql.=" `".$this->connection->escapeString($where[0])."` IN ('".implode("','",$where[2])."') $dir";
								break;
							case 'not_in':
								$sql.=" `".$this->connection->escapeString($where[0])."` NOT IN ('".implode("','",$where[2])."') $dir";
								break;
							case '=':
							case '!=':
							case '>=':
							case '<=':
							case '<':
							case '>':
								$sql.=" `".$this->connection->escapeString($where[0])."`".$this->connection->escapeString($where[1])."'".$this->connection->escapeString($where[2])."' $dir";
								break;
							}
						}
					$sql=preg_replace("/{$dir}$/",'',$sql);
					}
				else
					{
					switch($data['where'][1])
						{
						case 'in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` IN ('".implode("','",$data['where'][2])."')";
							break;
						case 'not_in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` NOT IN ('".implode("','",$data['where'][2])."')";
							break;
						case '=':
						case '!=':
						case '>=':
						case '<=':
						case '<':
						case '>':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`".$this->connection->escapeString($data['where'][1])."'".$this->connection->escapeString($data['where'][2])."' ";
							break;
						}
					
					}
				$sql.=') ';
				}
//---GROUP BY claude
			if(isset($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escapeString($data['group_by']).'`';
				}
//---ORDER BY claude
			if(isset($data['order_by']))
				{
				if(is_array($data['order_by']))
					{
					$dir='ASC';
					if($data['order_by'][1]===1)
						{
						$dir='DESC';
						}
					else
						{
						throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Was set wrong order_by part');
						}
					$sql.=' ORDER BY `'.$this->connection->escapeString($data['order_by'][0]).'` '.$dir.' ';
					}
				else
					{
					$sql.=' ORDER BY `'.$this->connection->escapeString($data['order_by']).'` ';
					}
				}
//---LIMIT claude
			if(isset($data['limit']))
				{
				if(is_array($data['limit']))
					{
					$sql.=' LIMIT '.$this->connection->escapeString($data['limit'][0]).','.$this->connection->escapeString($data['limit'][1]);
					}
				else
					{
					$sql.=' LIMIT '.$this->connection->escapeString($data['limit']);
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if (!is_a($results,'SQLite3Result'))
					{
					throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return assoc array

		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			$return=NULL;
			while($result=$results->fetchArray(SQLITE3_ASSOC))
				{
				$return[]=$result;
				}
			return $return;
			}
		}



/*-------------------------------------------------------------------------
* Example of Select_Join function
*
* $db->Select_Join(array(
*							'tablename'=>'tablename',
*							'distinct',
* 							'fields'=>array(array('tablename1','field1'),array('tablename1','field2'),array('tablename2','field1')),
* 							'join'=>array(
* 											array(
* 												'left',
*												'tablename1',
* 												'field',
*												'tablename2',
* 												'field'
* 												),
* 											array(
* 												'inner',
*												'tablename1',
* 												'field',
*												'tablename2',
* 												'field'
* 												)
* 											)
* 							['join'=>array('inner','tablename1','field','tablename2','field')]
* 							'where'=>array(
*											array('table1','field1','=','value1'),
*											array('table1','field2','in','array')
*											array('table2','field1','!=','value2')
* 											),
* 							['where'=>array('table1','field1','=','value1'),]
* 							'group_by'=>array('table','field'),
* 							'order_by'=>array('table','field'),
* 							['order_by'=>array('table','field',1),]
*	 						'limit'=>array('value1','from')
* 							['limit'=>'value1']
* 							));
*
* Return: assoc array of result or NULL or throw an exception if error.
--------------------------------------------------------------------------
	public function Select_Join(array $data)
		{
		try
			{
//---SELECT claude
			$sql='SELECT ';
//---DISTINCT claude
			if(in_array('distinct',$data))
				{
				$sql.='DISTINCT ';
				}
//---fields claude
			if(isset($data['fields']) && is_array($data['fields']))
				{
				foreach($data['fields'] as $field)
					{
					$sql.='`'.$this->connection->escapeString($field[0]).'`.`'.$this->connection->escapeString($field[1]).'`,';
					}
				$sql=preg_replace('/\,$/',' ',$sql);
				}
			else
				{
				$sql.='* ';
				}
//---FROM claude
			$sql.='FROM ';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.='`'.$this->connection->escapeString($data['tablename']).'` ';
				}
			else
				{
				throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---JOIN claude
			if(isset($data['join']) && is_array($data['join']))
				{
				if(is_array($data['join'][0]))
					{
					foreach($data['join'] as $join)
						{
						switch($join[4])
							{
							case 'left':
								$sql.='LEFT';
								break;
							case 'right':
								$sql.='RIGHT';
								break;
							default:
								$sql.='INNER';
								break;
							}
						$sql.=' JOIN `'.$this->connection->escapeString($join[2]).'` ON(`'.$this->connection->escapeString($join[0]).'`.`'.$this->connection->escapeString($join[1]).'`=`'.$this->connection->escapeString($join[2]).'`.`'.$this->connection->escapeString($join[3]).'`) ';
						}
					}
				else
					{
					switch($data['join'][4])
						{
						case 'left':
							$sql.='LEFT';
							break;
						case 'right':
							$sql.='RIGHT';
							break;
						default:
							$sql.='INNER';
							break;
						}
					$sql.=' JOIN `'.$this->connection->escapeString($data['join'][2]).'` ON(`'.$this->connection->escapeString($data['join'][0]).'`.`'.$this->connection->escapeString($data['join'][1]).'`=`'.$this->connection->escapeString($data['join'][2]).'`.`'.$this->connection->escapeString($data['join'][3]).'`) ';
					}
				}

//---WHERE claude
			if(isset($data['where']) && is_array($data['where']))
				{
				$sql.='WHERE (';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$dir='AND';
						if(isset($where[4]) && $where[4]===1)
							{
							$dir='OR';
							}
						switch($where[2])
							{
							case 'in':
								$sql.=" `".$this->connection->escapeString($where[0])."`.`".$this->connection->escapeString($where[1])."` IN ('".implode("','",$where[3])."') $dir";
								break;
							case 'not_in':
								$sql.=" `".$this->connection->escapeString($where[0])."`.`".$this->connection->escapeString($where[1])."` NOT IN ('".implode("','",$where[3])."') $dir";
								break;
							case '=':
							case '!=':
							case '>=':
							case '<=':
							case '<':
							case '>':
								$sql.=" `".$this->connection->escapeString($where[0])."`.`".$this->connection->escapeString($where[1])."`".$this->connection->escapeString($where[2])."'".$this->connection->escapeString($where[3])."' $dir";
								break;
							}
						}
					$sql=preg_replace("/{$dir}$/",'',$sql);
					}
				else
					{
					switch($data['where'][2])
						{
						case 'in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`.`".$this->connection->escapeString($data['where'][1])."` IN ('".implode("','",$data['where'][3])."')";
							break;
						case 'not_in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`.`".$this->connection->escapeString($data['where'][1])."` NOT IN ('".implode("','",$data['where'][3])."')";
							break;
						case '=':
						case '!=':
						case '>=':
						case '<=':
						case '<':
						case '>':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`.`".$this->connection->escapeString($data['where'][1])."`".$this->connection->escapeString($data['where'][2])."'".$this->connection->escapeString($data['where'][3])."' ";
							break;
						}
					
					}
				$sql.=') ';
				}
//---GROUP BY claude
			if(isset($data['group_by']) && is_array($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escapeString($data['group_by'][0]).'`.`'.$this->connection->escapeString($data['group_by'][1]).'` ';
				}
//---ORDER BY claude
			if(isset($data['order_by']) && is_array($data['order_by']))
				{
				$dir='ASC';
				if(isset($data['order_by'][2]))
					{
					if($data['order_by'][2]===1)
						{
						$dir='DESC';
						}
					else
						{
						throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Was set wrong order_by part');
						}
					}
				$sql.=' ORDER BY `'.$this->connection->escapeString($data['order_by'][0]).'`.`'.$this->connection->escapeString($data['order_by'][1]).'` '.$dir.' ';
				}
//---LIMIT claude
			if(isset($data['limit']))
				{
				if(is_array($data['limit']))
					{
					$sql.=' LIMIT '.$this->connection->escapeString($data['limit'][0]).','.$this->connection->escapeString($data['limit'][1]);
					}
				else
					{
					$sql.=' LIMIT '.$this->connection->escapeString($data['limit']);
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if (!is_a($results,'SQLite3Result'))
					{
					throw new Db_Error('Error selecting data from the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return assoc array
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			while($result=$results->fetchArray(SQLITE3_ASSOC))
				{
				$return[]=$result;
				}
			return $return;
			}
		}



/*-------------------------------------------------------------------------
* Example of Delete function
*
* $db->Delete(array(
*					'tablename'=>'tablename',
* 					'where'=>array(
*									array('field1','=','value1',1),
*									array('field2','<','value2')
* 									)
* 					['where'=>array('field1','=','value1')]
* 					));
*
* Return: true or throw an exception if error.
--------------------------------------------------------------------------
	public function Delete(array $data)
		{
		try
			{
//---DELETE claude
			$sql='DELETE ';
//---FROM claude
			$sql.=' FROM ';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.='`'.$this->connection->escapeString($data['tablename']).'` ';
				}
			else
				{
				throw new Db_Error('Error deleting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---WHERE claude
			if(isset($data['where']) && is_array($data['where']))
				{
				$sql.='WHERE (';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$dir='AND';
						if(isset($where[3]) && $where[3]===1)
							{
							$dir='OR';
							}
						switch($where[1])
							{
							case 'in':
								$sql.=" `".$this->connection->escapeString($where[0])."` IN ('".implode("','",$where[2])."') $dir";
								break;
							case 'not_in':
								$sql.=" `".$this->connection->escapeString($where[0])."` NOT IN ('".implode("','",$where[2])."') $dir";
								break;
							case '=':
							case '!=':
							case '>=':
							case '<=':
							case '<':
							case '>':
								$sql.=" `".$this->connection->escapeString($where[0])."`".$this->connection->escapeString($where[1])."'".$this->connection->escapeString($where[2])."' $dir";
								break;
							}
						}
					$sql=preg_replace("/{$dir}$/",'',$sql);
					}
				else
					{
					switch($data['where'][1])
						{
						case 'in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` IN ('".implode("','",$data['where'][2])."')";
							break;
						case 'not_in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` NOT IN ('".implode("','",$data['where'][2])."')";
							break;
						case '=':
						case '!=':
						case '>=':
						case '<=':
						case '<':
						case '>':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`".$this->connection->escapeString($data['where'][1])."'".$this->connection->escapeString($data['where'][2])."' ";
							break;
						}
					
					}
				$sql.=') ';
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$result=$this->connection->query($sql);
//---if any error - throw exception
				if (!is_a($result,'SQLite3Result'))
					{
					throw new Db_Error('Error deleting data from the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			return true;
			}
		}



/*-------------------------------------------------------------------------
* Example of Count function
*
* $db->Count(array(
*					'tablename'=>'tablename',
*					'distinct',
* 					'where'=>array(
*									array('field1','=','value1',1),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'group_by'=>'field'
* 					));
*
* Return: int of count.
--------------------------------------------------------------------------
	public function Count(array $data)
		{
		try
			{
//---SELECT claude
			$sql='SELECT ';
//---DISTINCT claude
			if(in_array('distinct',$data))
				{
				$sql.='DISTINCT ';
				}
//---FROM claude
			$sql.='COUNT(*) AS count FROM ';
//---tablename claude
			if(isset($data['tablename']))
				{
				$sql.='`'.$this->connection->escapeString($data['tablename']).'` ';
				}
			else
				{
				throw new Db_Error('Error deleting data fron the table '.$data['tablename'].' in the '.__METHOD__,'Syntax error. Wasn`t set tablename');
				}
//---WHERE claude
			if(isset($data['where']) && is_array($data['where']))
				{
				$sql.='WHERE (';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$dir='AND';
						if(isset($where[3]) && $where[3]===1)
							{
							$dir='OR';
							}
						switch($where[1])
							{
							case 'in':
								$sql.=" `".$this->connection->escapeString($where[0])."` IN ('".implode("','",$where[2])."') $dir";
								break;
							case 'not_in':
								$sql.=" `".$this->connection->escapeString($where[0])."` NOT IN ('".implode("','",$where[2])."') $dir";
								break;
							case '=':
							case '!=':
							case '>=':
							case '<=':
							case '<':
							case '>':
								$sql.=" `".$this->connection->escapeString($where[0])."`".$this->connection->escapeString($where[1])."'".$this->connection->escapeString($where[2])."' $dir";
								break;
							}
						}
					$sql=preg_replace("/{$dir}$/",'',$sql);
					}
				else
					{
					switch($data['where'][1])
						{
						case 'in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` IN ('".implode("','",$data['where'][2])."')";
							break;
						case 'not_in':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."` NOT IN ('".implode("','",$data['where'][2])."')";
							break;
						case '=':
						case '!=':
						case '>=':
						case '<=':
						case '<':
						case '>':
							$sql.=" `".$this->connection->escapeString($data['where'][0])."`".$this->connection->escapeString($data['where'][1])."'".$this->connection->escapeString($data['where'][2])."' ";
							break;
						}
					
					}
				$sql.=') ';
				}
//---GROUP BY claude
			if(isset($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escapeString($data['group_by']).'`';
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if (!is_a($results,'SQLite3Result'))
					{
					throw new Db_Error('Error deleting data fron the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->lastErrorMsg());
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
//---else return assoc array
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			$result=$results->fetchArray(SQLITE3_ASSOC);
			return $result['count'];
			}
		}



/*-------------------------------------------------------------------------
* Example of Last_Insert_Id function
*
* $db->Last_Insert_Id();
*
* Return: int autoincrement id of last query.
--------------------------------------------------------------------------
	public function Last_Insert_Id()
		{
		return $this->connection->lastInsertId();
		}



/*-------------------------------------------------------------------------
* Example of Query function
*
* $db->Query($sql);
*
* Return: result from database.
--------------------------------------------------------------------------
	public function Query($sql)
		{
		try
			{
			$result=$this->connection->query($sql);
			if(!is_a($result,'SQLite3Result'))
				{
				throw new Db_Error('Error in the query in the '.__METHOD__,$this->connection->lastErrorMsg());
				}			
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$e->Error();
			}
		return $result;
		}



/*-------------------------------------------------------------------------
* Destructor of MySQL database driver
--------------------------------------------------------------------------*
	public function __destruct()
		{
		try
			{
			if(is_a($this->connection,'SQLite3'))
				{
				//$this->connection->close();
				}
			else
				{
				throw new Db_Error('Error creating database');
				}
			}
		catch (Db_Error $e)
			{
			$e->Error();
			}
		}*/
	}
