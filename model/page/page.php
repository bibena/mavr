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


class Page_Model extends Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of User_Model
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		$this->view=new View;
		}



/*-------------------------------------------------------------------------
* Example of Error function
*
* Page_Model::Error();
*
* Display error page.
--------------------------------------------------------------------------*/
	function Error(array $args)
		{
		try
			{
			global $session;
			list($number,$error_message)=$args;
			if(is_numeric($number))
				{
//include css and js
				$content['assets']=implode("\n",$this->assets)."\n";
				$content['assets'].='<meta http-equiv="Refresh" content="6; URL='.SUB_DIR.'">';
				switch($number)
					{
					case 404:
						$content['error']=ERROR_404_MESSAGE;
						header('HTTP/1.0 404');
						break;
					case 500:
						$content['error_message']=$session->Get('last_error');
						if(isset($error_message) && $error_message)
							{
							$content['error_message']=$error_message;
							}
						$content['error']=ERROR_500_MESSAGE;
						header('HTTP/1.0 500');
						break;
					}
				$content['content']=$this->view->Content_Create(__METHOD__,array());
//create content
				return $content;
				}
			else
				{
				throw new Error(ERROR_IN_ERROR_CATCHER);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}



/*-------------------------------------------------------------------------
* Example of __call method
*
* Class::Unknown_Method();
*
* Return: throw an exception.
--------------------------------------------------------------------------*/
	function __call($name, $arguments)
		{
//---if were calling unknown method throw an exception
		try
			{
			global $sql;
//include css and js
			$content["assets"]=implode("\n",$this->assets)."\n";
//select content if it exists.
			$page_array=$sql->Select(array("tablename"=>'pages',
											"fields"=>array('content'),
											"where"=>array(array("field"=>'alias',
																"symbol"=>'=',
																"value"=>$name),
															array("field"=>'is_visible',
																"symbol"=>'=',
																"value"=>1),
															array("field"=>'is_deleted',
																"symbol"=>'=',
																"value"=>0)),
											"single"=>'single'));
//create content
			if(!($page_array && is_array($page_array) && isset($page_array["content"])))
				{
				return $this->Error(array(404));
				}
			$content["content"]=$this->view->Content_Create(__METHOD__,$page_array["content"]);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}
	}
