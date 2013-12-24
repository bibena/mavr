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
* User model
*
* User_Model::Registration();
*
--------------------------------------------------------------------------*/


class Ajax_Model
	{
/*-------------------------------------------------------------------------
* Constructor of User_Model
--------------------------------------------------------------------------*/
	function Link(array $args)
		{
		try
			{
			list($link)=$args;
			$link=trim($link,'/');
			$link_array=explode('/',$link);
			switch(count($link_array))
				{
				case 0:
					throw new Error('Wrong URI');
					break;
				case 1: 
					$model='page';
					$method='main';
					break;
				case 3:
					$argument=$link_array[2];
				case 2:
					$model=$link_array[0];
					$method=$link_array[1];
					break;
				default:
					break;
				}
//---processing the request
			$cname=ucfirst($model).'_Model';
			$mname=implode('_',array_map('ucfirst',explode('_',$method)));
//---create example of the requested class
			$class=new Model;
//---call requested method in class
			if(isset($argument))
				{
				$content=$class->$mname($cname,array($argument));
				}
			else
				{
				$content=$class->$mname($cname);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content['content'];
		}	
	}
?>