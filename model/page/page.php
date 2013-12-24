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
	function Show()
		{
		echo "Method ".__METHOD__."was started";
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
						$content['error_message']=$error_message;
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
				throw new Error('Wrong parameter number in '.__METHOD__.'. It should be numeric.');
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		}
	function Main()
		{
//include css and js
		$content['assets']=implode("\n",$this->assets)."\n";
//create content
		$content['content']=$this->view->Content_Create(__METHOD__,array());
		return $content;
		}
	function Logout()
		{
		echo "Запущен метод ".__METHOD__;
		}
	function Update()
		{
		echo "Запущен метод ".__METHOD__;
		}
	}