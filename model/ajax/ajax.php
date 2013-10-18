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
	function Link($link)
		{
		try
			{
			$link=trim($link,'/');
			$link_array=explode('/',$link);
			switch(count($link_array))
				{
				case 0:
					throw new Error('Wrong URI');
					break;
				case 1: 
					$controller='page';
					$method='main';
					break;
				case 3:
					$argument=$link_array[2];
				case 2:
					$controller=$link_array[0];
					$method=$link_array[1];
					break;
				default:
					break;
				}
	//---processing the request
			$cname=ucfirst($controller).'_Model';
			$mname=implode('_',array_map('ucfirst',explode('_',$method)));
	//---create example of the requested class
			$class=new $cname;
	//---call requested method in class
			if(isset($argument))
				{
				$content=$class->$mname($argument);
				}
			else
				{
				$content=$class->$mname();
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