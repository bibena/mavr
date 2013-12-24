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


class Admin_Model extends Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of User_Model
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		$this->view=new View;
		$this->form=parent::Check();
		require_once('helper.php');
		$this->helper=new Admin_Helper;
		}
	function Show()
		{
		echo "Method ".__METHOD__."was started";
		}



	function Main($include='')
		{
		try
			{
			$content['assets']=implode("\n",$this->assets)."\n";
				{
				$content['content']=$this->view->Content_Create(__METHOD__,$include);
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}
/*-------------------------------------------------------------------------
* Example of Error function
*
* Page_Model::Error();
*
* Display error page.
--------------------------------------------------------------------------*/
	function Config()
		{
		try
			{
			if(count($this->form)>0)
				{
				$error=$this->helper->Check_For_Errors($this->form);
				if($error)
					{
//else print the form with error message
					$include=$this->view->Content_Create(__METHOD__,$this->helper->Display_Config($this->form));
					}
				else
					{
					$old_config=explode("\n",file_get_contents(ROOT_DIR.DS.'config'));
					foreach($old_config as $old_string)
						{
						$params=explode(' = ',$old_string);
						if(isset($this->form[$params[0]]))
							{
							$new_string[]=implode(' = ',array($params[0],$this->form[$params[0]]));
							}
						else
							{
							$new_string[]=$old_string;
							}
						}
					file_put_contents(ROOT_DIR.DS.'config',implode("\n",$new_string));
					$include=$this->view->Content_Create(__METHOD__,$this->helper->Display_Config());
					}
				}
			else
				{
				$include=$this->view->Content_Create(__METHOD__,$this->helper->Display_Config());				
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($include);
		}


	function Menu()
		{
		try
			{
			if(count($this->form)>0)
				{
				$include=$this->view->Content_Create(__METHOD__,$this->helper->Check_Menu($this->form));
				}
			else
				{
				$include=$this->view->Content_Create(__METHOD__,$this->helper->Display_Menu());
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($include);
		}


	function Shop()
		{
		try
			{
			if(count($this->form)>0)
				{
				$include=$this->view->Content_Create(__METHOD__,$this->helper->Check_Shop($this->form));
				}
			else
				{
				$include=$this->view->Content_Create(__METHOD__,$this->helper->Display_Shop());
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($include);
		}
	}
