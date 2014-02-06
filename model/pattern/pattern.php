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
		try
			{
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
		catch(Error $e)
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
	protected function Check()
		{
		try
			{
			global $session;
			if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false)
				{
				throw new Error('Wrong URL. Form wasn`t sent from this site.');
				}
			else
				{
				$form=array();
				if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['flag']) && $_POST['flag'])
					{
//---upload files to /temp dir
					foreach($_FILES as $files)
						{
						if($files['tmp_name'])
							{
							if(is_array($files["name"]))
								{
								foreach ($files["error"] as $key => $error)
									{
									if ($error == UPLOAD_ERR_OK) 
										{
										move_uploaded_file($files['tmp_name'][$key],ROOT_DIR.DS.'temp'.DS.call_user_func('end',explode('/',$files['tmp_name'][$key])));
										}
									else
										{
										//throw new Error('Error during uploadding group of files.');
										}
									}
								}
							else
								{
								if ($files["error"] == UPLOAD_ERR_OK) 
									{
									move_uploaded_file($files['tmp_name'],ROOT_DIR.DS.'temp'.DS.call_user_func('end',explode('/',$files['tmp_name'])));
									}
								else
									{
									//throw new Error('Error during uploadding file.');
									}
								}
							}
						}
					$_POST['postfiles']=$_FILES;
					$session->Set($_POST,'form',$_SERVER["REQUEST_URI"]);
					Redirect::Page('.');
					}
				if($_SERVER['REQUEST_METHOD']==='GET' && $session->Check('form',$_SERVER['REQUEST_URI']))
					{
					$form=$session->Get('form',$_SERVER['REQUEST_URI']);
					$session->Erase('form',$_SERVER['REQUEST_URI']);
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
