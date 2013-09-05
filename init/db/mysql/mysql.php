<?php

try{
	if(!defined('FLAG'))
		{
		throw new Flag_Error();
		}
	}
catch (Flag_Error $e)
	{
	echo $e;
	exit();
	}

/*-------------------------------------------------------------------------
* MySQL database driver
*
* Db::Get_Instance();
*
--------------------------------------------------------------------------*/


class Mysql_Db extends Pattern_Db
	{
	public static $count_last;
/*-------------------------------------------------------------------------
* Constructor of MySQL database driver
--------------------------------------------------------------------------*/
	public function __construct()
		{
		try
			{
//---create variable with config
			$this->config=Config::Get_Instance()->Get_Config();
//---create variable with connection
			$this->connection=@new mysqli($this->config['dbhost'],$this->config['dbuser'],$this->config['dbpassword']);
//---if any error in connection throw the exception
			if(mysqli_connect_errno())
				{
				throw new mysqli_sql_exception('Ошибка при подключении к базе данных');
				}
			}
		catch (mysqli_sql_exception $e)
			{
			echo 'Исключение метода '.__METHOD__.' класса '.__CLASS__.' : ',  $e->getMessage(), '<br>';
			if($this->config['show_error'])
				{
				printf("\n%s", mysqli_connect_errno());
				}
			exit();
			}		
		try
			{
//---set charset of connection
			if (!$this->connection->set_charset("utf8")) 
				{
				throw new Db_Error('Error choosing charset in the '.__METHOD__,$this->connection->error);
				}
//---check or create database
			if(!$this->Create_Database())
				{
				throw new Db_Error('Error checking database in the '.__METHOD__,$this->connection->error);
				}
//---if database OK - select it
			if(!$this->connection->select_db($this->config['dbname']))
				{
				throw new Db_Error('Error choosing database in the '.__METHOD__,$this->connection->error);
				}
			}
//---if any error - throw the exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
		}



/*-------------------------------------------------------------------------
* Example of Create_Database function
*
* $db->Create_Database();
*
* Return: true.
--------------------------------------------------------------------------*/
	public function Create_Database()
		{
//---check or create database
		$query="CREATE DATABASE IF NOT EXISTS `".$this->config['dbname']."` CHARACTER SET `utf8` COLLATE `utf8_general_ci`;";
		$query=$this->connection->escape_string($query);
//---return answer from database (true)
		return $this->connection->query($query);
		}



/*-------------------------------------------------------------------------
* Example of Create_Table function
*
* $db->Create_Table(array(
*							'tablename'=>'tablename',
*							'temporary',
* 							'if_not_exists',
* 							'column'=>array(
*											array('name'=>'id','type'=>'int','length'=>'10','default'=>'10','not_null','ai'),
*											array('name'=>'a','type'=>'int','length'=>'2','not_null'),
*											array('name'=>'b','type'=>'int','length'=>'4','not_null')
* 											),
* 							'primary_key'=>array('id'),
* 							'unique'=>array('a','b'),
* 							'foreign_key'=>array(
* 												array('key','table','key','on_update'=>'cascade','on_delete'=>'restrict'),
* 												array('key','table','key')
* 												),
* 							['foreign_key'=>array('key','table','key','on_update'=>'cascade','on_delete'=>'restrict'),]
* 							'engine'=>'innodb',
* 							'character_set'=>'utf8',
* 							'collate'=>'utf8_general_ci',
* 							));
*
* Return: bool true.
--------------------------------------------------------------------------*/
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
			$sql.=' `'.$data['tablename'].'` (';
//---colunn claude
			if(isset($data['column']))
				{
				$types=array('bit','tinyint','smallint','mediumint','int','integer','bigint','real','double','float','decimal','numeric','date','time','timestamp','datetime','year','char','varchar','binary','varbinary','tinyblob','blob','mediumblob','longblob','tinytext','text','mediumtext','longtext');
				foreach($data['column'] as $column)
					{
					$sql.="`".$this->connection->escape_string($column['name'])."`";
					if(isset($column['type']) && in_array($column['type'],$types))
						{
						$sql.=' '.strtoupper($column['type']);
						}
					if(isset($column['length']))
						{
						$sql.=' ('.($column['length']).')';
						}
					if(in_array('not_null',$column))
						{
						$sql.=' NOT NULL';
						}
					if(in_array('ai',$column))
						{
						if(!in_array('not_null',$column))
							{
							$sql.=' NOT NULL';
							}
						$sql.=' AUTO_INCREMENT';
						}
					elseif(isset($column['default']))
						{
						$sql.=" DEFAULT '".$this->connection->escape_string($column['default'])."'";
						}
					$sql.=',';
					}
				}
//---PRIMARY KEY claude
			if(isset($data['primary_key']))
				{
				$sql.=" PRIMARY KEY (`".implode("`,`",$data['primary_key'])."`),";
				}
//---UNIQUE claude
			if(isset($data['unique']))
				{
				$sql.=" UNIQUE ('".implode("`,`",$data['unique'])."`),";
				}
//---FOREIGN KEY claude
			if(isset($data['foreign_key']))
				{
				if(is_array($data['foreign_key'][0]))
					{
					foreach($data['foreign_key'] as $fkey)
						{
						$sql.=' FOREIGN KEY ('.$this->connection->escape_string($fkey[0]).') REFERENCES '.$this->connection->escape_string($fkey[1]).'('.$this->connection->escape_string($fkey[2]).')';
						if(isset($fkey['on_update']))
							{
							$sql.=' ON UPDATE '.$this->connection->escape_string($fkey['on_update']);
							}
						if(isset($fkey['on_delete']))
							{
							$sql.=' ON DELETE '.$this->connection->escape_string($fkey['on_delete']);
							}
						$sql.=',';
						}
					}
				else
					{
					$sql.=' FOREIGN KEY ('.$this->connection->escape_string($data['foreign_key'][0]).') REFERENCES '.$this->connection->escape_string($data['foreign_key'][1]).'('.$this->connection->escape_string($data['foreign_key'][2]).')';
					if(isset($data['foreign_key']['on_update']))
						{
						$sql.=' ON UPDATE '.$this->connection->escape_string($data['foreign_key']['on_update']);
						}
					if(isset($data['foreign_key']['on_delete']))
						{
						$sql.=' ON DELETE '.$this->connection->escape_string($data['foreign_key']['on_delete']);
						}
					}
				}
			$sql=preg_replace('/\,$/','',$sql);
			$sql.=')';
//---ENGINE claude
			if(isset($data['engine']))
				{
				$sql.=' ENGINE='.$this->connection->escape_string($data['engine']);
				}
			else
				{
				$sql.=' ENGINE=InnoDB';
				}
//---CHARACTER SET claude
			if(isset($data['character_set']))
				{
				$sql.=' CHARACTER SET='.$this->connection->escape_string($data['character_set']);
				}
			else
				{
				$sql.=' CHARACTER SET=UTF8';
				}
//---COLLATE claude
			if(isset($data['collate']))
				{
				$sql.=' COLLATE='.$this->connection->escape_string($data['collate']);
				}
			else
				{
				$sql.=' COLLATE=utf8_general_ci';
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				if (!$this->connection->query($sql))
					{
					throw new Db_Error('Error creating the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
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
*						'temporary',
*						'if_exists'
*						);
*
* Return: bool true.
--------------------------------------------------------------------------*/
	public function Drop_Table(array $data)
		{
		try
			{
//---DROP claude
			$sql='DROP';
//---TEMPORARY claude
			if(in_array('temporary',$data))
				{
				$sql.=' TEMPORARY';
				}
//---TABLE claude
			$sql.=' TABLE';
//---IF EXISTS claude
			if(in_array('if_exists',$data))
				{
				$sql.=' IF EXISTS';
				}
//---tablename claude
			$sql.=' `'.$data['tablename'].'`;';
//---send query
			if(!in_array('query',$data))
				{
				if (!$this->connection->query($sql))
					{
					throw new Db_Error('Error deleting the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
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
*					'low_priority',
*					'delayed',
*					'ignore',
* 					'set'=>array('title1'=>'set1','title2'=>'set2')
* 					));
*
* Return: int number of inserted row.
--------------------------------------------------------------------------*/
	public function Insert(array $data)
		{
		try
			{
//---INSERT claude
			$sql='INSERT';
//---LOW PRIORITY claude
			if(in_array('low_priority',$data))
				{
				$sql.=' LOW_PRIORITY';
				}
//---DELAYED claude
			elseif(in_array('delayed',$data))
				{
				$sql.=' DELAYED';
				}
//---IGNORE claude
			if(in_array('ignore',$data))
				{
				$sql.=' IGNORE';
				}
//---INTO claude
			$sql.=' INTO `'.$data['tablename'].'`';
//---SET claude
			if(isset($data['set']))
				{
				$sql.=' SET';
				foreach($data['set'] as $title=>$set)
					{
					$sql.=" `".$this->connection->escape_string($title)."`='".$this->connection->escape_string($set)."',";
					}
				$sql=preg_replace('/\,$/',';',$sql);
				}
//---send query
			if(!in_array('query',$data))
				{
				if (!$this->connection->query($sql))
					{
					throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			self::$count_last=$this->connection->affected_rows;
			return $this->connection->affected_rows;
			}
		}



/*-------------------------------------------------------------------------
* Example of Multi_Insert function
*
* $db->Multi_Insert(array(
* *							'tablename'=>'tablename',
* 							array(
*									'low_priority',
*									'delayed',
*									'ignore',
* 									'set'=>array('title1'=>'set1','title2'=>'set2')
* 									),
* 							array(
*									'low_priority',
*									'delayed',
*									'ignore',
* 									'set'=>array('title1'=>'set1','title2'=>'set2')
* 									)
* 							);
*
* Return: int number of inserted row.
--------------------------------------------------------------------------*/
	public function Multi_Insert(array $data)
		{
		try
			{
//---start transaction
			$this->connection->autocommit(false);
			foreach($data as $query)
				{
//---INSERT claude
				$sql='INSERT';
//---LOW PRIORITY claude
				if(in_array('low_priority',$query))
					{
					$sql.=' LOW_PRIORITY';
					}
//---DELAYED claude
				elseif(in_array('delayed',$query))
					{
					$sql.=' DELAYED';
					}
//---IGNORE claude
				if(in_array('ignore',$query))
					{
					$sql.=' IGNORE';
					}
//---INTO claude
				$sql.=' INTO `'.$data['tablename'].'`';
//---SET claude
				if(isset($query['set']) && is_array($query['set']))
					{
					$sql.=' SET';
					foreach($query['set'] as $title=>$set)
						{
						$sql.=" `".$this->connection->escape_string($title)."`='".$this->connection->escape_string($set)."',";
						}
					$sql=preg_replace('/\,$/',';',$sql);
					}
//---send query
				if(!in_array('query',$data))
					{
					if (!$this->connection->query($sql))
						{
						throw new Db_Error('Error inserting data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
						}
					@$count_last+=$this->connection->affected_rows;
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			$this->connection->rollback();
			echo $e;
			exit();
			}
//---else commit transaction and return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			$this->connection->commit();
			self::$count_last=$count_last;
			return $count_last;
			}
		}



/*-------------------------------------------------------------------------
* Example of Replace function
*
* $db->Replace(array(
*						'tablename'=>'tablename',
*						'low_priority',
*						'delayed',
* 						'set'=>array('title1'=>'set1','title2'=>'set2')
* 						));
*
* Return: int number of replaced row.
--------------------------------------------------------------------------*/
	public function Replace(array $data)
		{
		try
			{
//---REPLACE claude
			$sql='REPLACE';
//---LOW PRIORITY claude
			if(in_array('low_priority',$data))
				{
				$sql.=' LOW_PRIORITY';
				}
//---DELAYED claude
			elseif(in_array('delayed',$data))
				{
				$sql.=' DELAYED';
				}
//---INTO claude
			$sql.=' INTO `'.$data['tablename'].'`';
//---SET claude
			if(isset($data['set']))
				{
				$sql.=' SET';
				foreach($data['set'] as $title=>$set)
					{
					$sql.=" `".$this->connection->escape_string($title)."`='".$this->connection->escape_string($set)."',";
					}
				$sql=preg_replace('/\,$/',';',$sql);
				}
//---send query
			if(!in_array('query',$data))
				{
				if (!$this->connection->query($sql))
					{
					throw new Db_Error('Error replacing data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			self::$count_last=$this->connection->affected_rows;
			return (($this->connection->affected_rows)/2);
			}
		}



/*-------------------------------------------------------------------------
* Example of Update function
*
* $db->Update(array(
*					'tablename'=>'tablename',
*					'low_priority',
*					'ignore',
* 					'set'=>array('title1'=>'set1','title2'=>'set2'),
* 					'where'=>array(
*									array('field1','=','value1'),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'order_by'=>array('field','ASC'),
* 					'limit'=>array('value1','from')
* 					['limit'=>'value1']
* 					));
*
* Return: int number of updated row.
--------------------------------------------------------------------------*/
	public function Update(array $data)
		{
		try
			{
//---SELECT claude
			$sql='UPDATE ';
//---LOW_PRIORITY claude
			if(in_array('low_priority',$data))
				{
				$sql.='LOW_PRIORITY ';
				}
//---IGNORE claude
			if(in_array('ignore',$data))
				{
				$sql.='IGNORE ';
				}
//---tablename claude
			$sql.='`'.$data['tablename'].'` ';
//---SET claude
				if(isset($data['set']) && is_array($data['set']))
					{
					$sql.='SET';
					foreach($data['set'] as $title=>$set)
						{
						$sql.=" `".$this->connection->escape_string($title)."`='".$this->connection->escape_string($set)."',";
						}
					$sql=preg_replace('/\,$/',' ',$sql);
					}
//---WHERE claude
			if(isset($data['where']))
				{
				$sql.='WHERE';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$sql.=" `".$this->connection->escape_string($where[0])."`".$this->connection->escape_string($where[1])."'".$this->connection->escape_string($where[2])."' AND";
						}
					$sql=preg_replace('/AND$/',' ',$sql);
					}
				else
					{
					$sql.=" `".$this->connection->escape_string($data['where'][0])."`".$this->connection->escape_string($data['where'][1])."'".$this->connection->escape_string($data['where'][2])."' ";
					}
				}
//---ORDER BY claude
			if(isset($data['order_by']) && is_array($data['order_by']))
				{
				$sql.='ORDER BY `'.$this->connection->escape_string($data['order_by'][0]).'` '.$this->connection->escape_string($data['order_by'][1]);
				}
//---LIMIT claude
			if(isset($data['limit']))
				{
				if(is_array($data['limit']))
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit'][0]).','.$this->connection->escape_string($data['limit'][1]);
					}
				else
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit']);
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if ($this->connection->error)
					{
					throw new Db_Error('Error updating data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			self::$count_last=$this->connection->affected_rows;
			return $this->connection->affected_rows;
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
*									array('field1','=','value1'),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'group_by'=>array('field','ASC'),
* 					'order_by'=>array('field','ASC'),
* 					'limit'=>array('value1','from')
* 					['limit'=>'value1']
* 					));
*
* Return: assoc array of result or NULL.
--------------------------------------------------------------------------*/
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
			$sql.=' FROM `'.$data['tablename'].'` ';
//---WHERE claude
			if(isset($data['where']))
				{
				$sql.='WHERE';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$sql.=" `".$this->connection->escape_string($where[0])."`".$this->connection->escape_string($where[1])."'".$this->connection->escape_string($where[2])."' AND";
						}
					$sql=preg_replace('/AND$/','',$sql);
					}
				else
					{
					$sql.=" `".$this->connection->escape_string($data['where'][0])."`".$this->connection->escape_string($data['where'][1])."'".$this->connection->escape_string($data['where'][2])."' ";
					}
				}
//---GROUP BY claude
			if(isset($data['group_by']) && is_array($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escape_string($data['group_by'][0]).'` '.$this->connection->escape_string($data['group_by'][1]);
				}
//---ORDER BY claude
			if(isset($data['order_by']) && is_array($data['order_by']))
				{
				$sql.=' ORDER BY `'.$this->connection->escape_string($data['order_by'][0]).'` '.$this->connection->escape_string($data['order_by'][1]);
				}
//---LIMIT claude
			if(isset($data['limit']))
				{
				if(is_array($data['limit']))
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit'][0]).','.$this->connection->escape_string($data['limit'][1]);
					}
				else
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit']);
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if ($this->connection->error)
					{
					throw new Db_Error('Error selecting data fron the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return assoc array
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			while($result=$results->fetch_assoc())
				{
				$return[]=$result;
				}
			self::$count_last=$results->num_rows;
			return $return;
			}
		}



/*-------------------------------------------------------------------------
* Example of Select_Join function
*
* $db->Select_Join(array(
*							'tablename'=>'tablename',
*							'distinct',
* 							'fields'=>array('tablename1'=>'field1','tablename1'=>'field2','tablename2'=>'field1'),
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
*											array('table1','field2','<','value2')
*											array('table2','field1','!=','value2')
* 											),
* 							['where'=>array('table1','field1','=','value1'),]
* 							'group_by'=>array('table1','field1','ASC'),
* 							'order_by'=>array('table1','field1','ASC'),
* 							'limit'=>array('value1','from')
* 							['limit'=>'value1']
* 							));
*
* Return: assoc array of result or NULL.
--------------------------------------------------------------------------*/
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
				foreach($data['fields'] as $tab=>$field)
					{
					$sql.='`'.$this->connection->escape_string($tab).'`.`'.$this->connection->escape_string($field).'`,';
					}
				$sql=preg_replace('/\,$/','',$sql);
				}
			else
				{
				$sql.='*';
				}
//---FROM claude
			$sql.=' FROM `'.$data['tablename'].'` ';
//---JOIN claude
			if(isset($data['join']) && is_array($data['join']))
				{
				if(is_array($data['join'][0]))
					{
					foreach($data['join'] as $join)
						{
						switch($join[0])
							{
							case 'left':
								$sql.='LEFT';
								break;
							case 'right':
								$sql.='RIGHT';
								break;
							case 'inner':
								$sql.='INNER';
								break;
							}
						$sql.=' JOIN `'.$this->connection->escape_string($join[3]).'` ON(`'.$this->connection->escape_string($join[1]).'`.`'.$this->connection->escape_string($join[2]).'`=`'.$this->connection->escape_string($join[3]).'`.`'.$this->connection->escape_string($join[4]).'`),';
						}
					$sql=preg_replace('/\,$/',' ',$sql);
					}
				else
					{
					switch($data['join'][0])
						{
						case 'left':
							$sql.='LEFT';
							break;
						case 'right':
							$sql.='RIGHT';
							break;
						case 'inner':
							$sql.='INNER';
							break;
						}
					$sql.=' JOIN `'.$this->connection->escape_string($data['join'][3]).'` ON(`'.$this->connection->escape_string($data['join'][1]).'`.`'.$this->connection->escape_string($data['join'][2]).'`=`'.$this->connection->escape_string($data['join'][3]).'`.`'.$this->connection->escape_string($data['join'][4]).'`) ';
					}
				}
//---WHERE claude
			if(isset($data['where']))
				{
				$sql.='WHERE';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$sql.=" `".$this->connection->escape_string($where[0])."`.`".$this->connection->escape_string($where[1])."`".$this->connection->escape_string($where[2])."'".$this->connection->escape_string($where[3])."' AND";
						}
					$sql=preg_replace('/AND$/','',$sql);
					}
				else
					{
					$sql.=" `".$this->connection->escape_string($data['where'][0])."`.`".$this->connection->escape_string($data['where'][1])."`".$this->connection->escape_string($data['where'][2])."'".$this->connection->escape_string($data['where'][3])."' ";
					}
				}
//---GROUP BY claude
			if(isset($data['group_by']) && is_array($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escape_string($data['group_by'][0]).'`.`'.$this->connection->escape_string($data['group_by'][1]).'` '.$this->connection->escape_string($data['group_by'][2]);
				}
//---ORDER BY claude
			if(isset($data['order_by']) && is_array($data['order_by']))
				{
				$sql.=' ORDER BY `'.$this->connection->escape_string($data['order_by'][0]).'`.`'.$this->connection->escape_string($data['order_by'][1]).'` '.$this->connection->escape_string($data['order_by'][2]);
				}
//---LIMIT claude
			if(isset($data['limit']))
				{
				if(is_array($data['limit']))
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit'][0]).','.$this->connection->escape_string($data['limit'][1]);
					}
				else
					{
					$sql.=' LIMIT '.$this->connection->escape_string($data['limit']);
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if ($this->connection->error)
					{
					throw new Db_Error('Error selecting data from the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return assoc array
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			while($result=$results->fetch_assoc())
				{
				$return[]=$result;
				}
			self::$count_last=$results->num_rows;
			return $return;
			}
		}



/*-------------------------------------------------------------------------
* Example of Delete function
*
* $db->Delete(array(
*					'tablename'=>'tablename',
*					'low_priorityd',
*					'ignore',
* 					'where'=>array(
*									array('field1','=','value1'),
*									array('field2','<','value2')
* 									)
* 					['where'=>array('field1','=','value1')]
* 					));
*
* Return: int number of deleted row.
--------------------------------------------------------------------------*/
	public function Delete(array $data)
		{
		try
			{
//---DELETE claude
			$sql='DELETE ';
//---LOW_PRIORITY claude
			if(in_array('low_priority',$data))
				{
				$sql.='LOW_PRIORITY ';
				}
//---IGNORE claude
			if(in_array('ignore',$data))
				{
				$sql.='IGNORE ';
				}
//---FROM claude
			$sql.=' FROM `'.$data['tablename'].'` ';
//---WHERE claude
			if(isset($data['where']))
				{
				$sql.='WHERE';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$sql.=" `".$this->connection->escape_string($where[0])."`".$this->connection->escape_string($where[1])."'".$this->connection->escape_string($where[2])."' AND";
						}
					$sql=preg_replace('/AND$/','',$sql);
					}
				else
					{
					$sql.=" `".$this->connection->escape_string($data['where'][0])."`".$this->connection->escape_string($data['where'][1])."'".$this->connection->escape_string($data['where'][2])."' ";
					}
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if ($this->connection->error)
					{
					throw new Db_Error('Error deleting data from the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return number of rows
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			self::$count_last=$this->connection->affected_rows;
			return $this->connection->affected_rows;
			}
		}



/*-------------------------------------------------------------------------
* Example of Count function
*
* $db->Count(array(
*					'tablename'=>'tablename',
*					'distinct',
* 					'where'=>array(
*									array('field1','=','value1'),
*									array('field2','<','value2')
* 									),
* 					['where'=>array('field1','=','value1'),]
* 					'group_by'=>array('field','ASC')
* 					));
*
* Return: int of count.
--------------------------------------------------------------------------*/
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
			$sql.='COUNT(*) AS count FROM `'.$data['tablename'].'` ';
//---WHERE claude
			if(isset($data['where']))
				{
				$sql.='WHERE';
				if(is_array($data['where'][0]))
					{
					foreach($data['where'] as $where)
						{
						$sql.=" `".$this->connection->escape_string($where[0])."`".$this->connection->escape_string($where[1])."'".$this->connection->escape_string($where[2])."' AND";
						}
					$sql=preg_replace('/AND$/','',$sql);
					}
				else
					{
					$sql.=" `".$this->connection->escape_string($data['where'][0])."`".$this->connection->escape_string($data['where'][1])."'".$this->connection->escape_string($data['where'][2])."' ";
					}
				}
//---GROUP BY claude
			if(isset($data['group_by']) && is_array($data['group_by']))
				{
				$sql.='GROUP BY `'.$this->connection->escape_string($data['group_by'][0]).'` '.$this->connection->escape_string($data['group_by'][1]);
				}
			$sql.=';';
//---send query
			if(!in_array('query',$data))
				{
				$results=$this->connection->query($sql);
//---if any error - throw exception
				if ($this->connection->error)
					{
					throw new Db_Error('Error counting data in the table '.$data['tablename'].' in the '.__METHOD__,$this->connection->error);
					}
				}
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
//---else return count
		if(in_array('query',$data))
			{
			return $sql;
			}
		else
			{
			$result=$results->fetch_assoc();
			return $result['count'];
			}
		}



/*-------------------------------------------------------------------------
* Example of Count_Last function
*
* $db->Count_Last();
*
* Return: int count of last query.
--------------------------------------------------------------------------*/
	public function Count_Last()
		{
		return self::$count_last;
		}



/*-------------------------------------------------------------------------
* Example of Insert_Id function
*
* $db->Insert_Id();
*
* Return: int autoincrement id of last query.
--------------------------------------------------------------------------*/
	public function Insert_Id()
		{
		return $this->connection->insert_id;
		}



/*-------------------------------------------------------------------------
* Example of Query function
*
* $db->Query($sql);
*
* Return: result from database.
--------------------------------------------------------------------------*/
	public function Query($sql)
		{
		try
			{
			$result=$this->connection->query($sql);
			if (!$result)
				{
				throw new Db_Error('Error in the query in the '.__METHOD__,$this->connection->error);
				}			
			}
//---if any error - throw exception
		catch (Db_Error $e)
			{
			echo $e;
			exit();
			}
		return $result;
		}



/*-------------------------------------------------------------------------
* Destructor of MySQL database driver
--------------------------------------------------------------------------*/
	public function __destruct()
		{
		$this->connection->close();
		}
	}
