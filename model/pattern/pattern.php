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
* Ancestor of all models
*
* new Pattern_Model;
*
--------------------------------------------------------------------------*/


class Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of Pattern_Model
--------------------------------------------------------------------------*/
	protected function __construct()
		{
		$this->session=new Session;
//read the name of template from config
		$config=Config::Get_Instance()->Get_Config();
		$this->dir=ROOT_DIR.DS.'template'.DS.$config['template'].DS;
//scan css and js dir of template
		$this->css=scandir($this->dir.'css');
		$this->js=scandir($this->dir.'js');
 //include all css and js file from theese dir
		$this->assets=array();
		foreach($this->js as $js)
			{
			if(!is_dir($this->dir.$js))
				{
				$this->assets[]='<script src="'.SUB_DIR.'template'.DS.$config['template'].DS.'js'.DS.$js.'"></script>';
				}
			}
		foreach($this->css as $css)
			{
			if(!is_dir($this->dir.$css))
				{
				$this->assets[]='<link rel="stylesheet" href="'.SUB_DIR.'template'.DS.$config['template'].DS.'css'.DS.$css.'" />';
				}
			}
		}




/*-------------------------------------------------------------------------
* Example of __call method
*
* Class::Unknown_Method();
*
* Return: throw an exception.
--------------------------------------------------------------------------*/
	protected function Check()
		{
		$form=array();
		try
			{
			if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false)
				{
				throw new Error('Wrong URL. Form wasn`t sent from this site.');
				}
			else
				{
				if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['flag']) && $_POST['flag'])
					{
					$_SESSION['form'][$_SERVER['REQUEST_URI']]=$_POST;
					Redirect::Page('.');
					}
				if($_SERVER['REQUEST_METHOD']==='GET' && isset($_SESSION['form'][$_SERVER['REQUEST_URI']]))
					{
					$form=$_SESSION['form'][$_SERVER['REQUEST_URI']];
					unset($_SESSION['form'][$_SERVER['REQUEST_URI']]);
					}
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $form;
		}



/*-------------------------------------------------------------------------
* Example of __call method
*
* Class::Unknown_Method();
*
* Return: throw an exception.
--------------------------------------------------------------------------*/
	public function __call($name, $arguments)
		{
//---if were calling unknown method throw an exception
		try
			{
			throw new Error('Wrong URL. Error calling unknow method '.$name);
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	}
