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
* Helper for user models
*
* new User_Helper;
*
--------------------------------------------------------------------------*/


class Admin_Helper
	{
/*-------------------------------------------------------------------------
* Constructor of User_Helper
--------------------------------------------------------------------------*/
	function __construct()
		{
		}



	function Yes_Or_No_Radio($param,$yes_no,$set)
		{
		$param_array['type']='radio';
		$param_array['name']=strtolower($param);
		$param_array['required']='required';
		$param_array['class']='form-control';
		$param_array['id']='input'.ucfirst($param);
		$param_array['value']=($yes_no==YES)?1:0;
		if($set==$param_array['value'])
			{
			$param_array['checked']='checked';
			}
		return Html::Tag('input',$param_array,$yes_no);
		}
	function Db_Type_Radio($param,$possible,$set)
		{
		$param_array['type']='radio';
		$param_array['name']=strtolower($param);
		$param_array['required']='required';
		$param_array['class']='form-control';
		$param_array['id']='input'.ucfirst($param);
		$param_array['value']=$possible;
		if($set==$possible)
			{
			$param_array['checked']='checked';
			}
		return Html::Tag('input',$param_array,$possible);
		}
	function Db_Type_Radio_Config($param,$set)
		{
		foreach(array('sqlite','mysql') as $possible)
			{
			$result[$possible]=$this->Put_In_Label(array('class'=>'radio-inline'),$this->Db_Type_Radio($param,$possible,$set));
			}
		return implode('',$result);
		}
	function Yes_No_Radio_Config($param,$set)
		{
		foreach(array(YES,NO) as $possible_value)
			{
			$temp[$possible_value]=$this->Put_In_Label(array('class'=>'radio-inline'),$this->Yes_Or_No_Radio($param,$possible_value,$set));
			}
		return implode('',$temp);
		}
	function Non_Required_Text_Config($param,$set)
		{
		$param_array['type']='input';
		$param_array['name']=$param;
		$param_array['class']='form-control';
		$param_array['id']='input'.ucfirst($param);
		$param_array['value']=$set;
		return Html::Tag('input',$param_array);
		}
	function Required_Text_Config($param,$set)
		{
		$param_array['type']='input';
		$param_array['name']=$param;
		$param_array['required']='required';
		$param_array['class']='form-control';
		$param_array['id']='input'.ucfirst($param);
		$param_array['value']=$set;
		return Html::Tag('input',$param_array);
		}
	function Dir_Radio($param,$dirname,$set)
		{
		$param_array['type']='radio';
		$param_array['name']=strtolower($param);
		$param_array['required']='required';
		$param_array['class']='form-control';
		$param_array['id']='input'.ucfirst($param);
		$param_array['value']=$dirname;
		if($set==$dirname)
			{
			$param_array['checked']='checked';
			}
		return Html::Tag('input',$param_array,$dirname);
		}
	function Dir_Radio_Config($param,$set)
		{
		if($handle=opendir(ROOT_DIR.DS.$param))
			{
			while(false !== ($entry = readdir($handle)))
				{
				if ($entry != "." && $entry != "..")
					{
					if(is_dir(ROOT_DIR.DS.$param.DS.$entry))
						{
						$dir_result[$entry]=$this->Put_In_Label(array('class'=>'radio-inline'),$this->Dir_Radio($param,$entry,$set));
						}
					}
				}
			closedir($handle);
			}
		return implode('',$dir_result);
		}
	function Put_In_Div($param,$content)
		{
		return Html::Tag('div',$param,$content);
		}
	function Put_In_Label($param,$content)
		{
		return Html::Tag('label',$param,$content);
		}
/*-------------------------------------------------------------------------
* Example of Check method
*
* User_Helper::Check(array('pass'=>array('password','string_for_check')));
*
* Return: array with bool result for every element.
--------------------------------------------------------------------------*/
	function Display_Config($form=false)
		{
		$config=Config::Get_Instance();
		$result=$config->Get_Config(true);
		$yes_no_radio=array('show_error','full_error_info','log');
		$db_type_radio=array('dbtype');
		$dir_radio=array('lang','template');
		$non_required_text=array('subdir','dbpath');
		foreach($result as $tab=>$group)
			{
			foreach($group as $param=>$value)
				{
				if(isset($form[$param]))
					{
					$value=$form[$param];
					}
				if(in_array($param,$yes_no_radio))
					{
					$result[$tab][$param]=$this->Yes_No_Radio_Config($param,$value);
					}
				elseif(in_array($param,$db_type_radio))
					{
					$result[$tab][$param]=$this->Db_Type_Radio_Config($param,$value);
					}
				elseif(in_array($param,$dir_radio))
					{
					$result[$tab][$param]=$this->Dir_Radio_Config($param,$value);
					}
				elseif(in_array($param,$non_required_text))
					{
					$result[$tab][$param]=$this->Non_Required_Text_Config($param,$value);
					}
				else
					{
					$result[$tab][$param]=$this->Required_Text_Config($param,$value);
					}
				}
			}
		if($form)
			{
			$result['error']=$this->Check_For_Errors($form);
			}
		return $result;	
		}
	function Check_For_Errors($form)
		{
		$error=array();
		if(!(isset($form['dbtype']) && in_array(strtolower($form['dbtype']),array('sqlite','mysql'))))
			{
			$error['dbtype']=1;
			}
		if(!isset($form['dbhost']))
			{
			$error['dbhost']=1;
			}
		if(!isset($form['dbname']))
			{
			$error['dbname']=1;
			}
		if(!isset($form['dbuser']))
			{
			$error['dbuser']=1;
			}
		if(!isset($form['dbpassword']))
			{
			$error['dbpassword']=1;
			}
		if(!isset($form['dbpath']) && $form['dbtype']=='sqlite')
			{
			$error['dbpath']=1;
			}
		if(!isset($form['template']))
			{
			$error['template']=1;
			}
		if(!(isset($form['show_error']) && in_array($form['show_error'],array(0,1))))
			{
			$error['show_error']=1;
			}
		if(!(isset($form['full_error_info']) && in_array($form['full_error_info'],array(0,1))))
			{
			$error['full_error_info']=1;
			}
		if(!(isset($form['log']) && in_array($form['log'],array(0,1))))
			{
			$error['log']=1;
			}
		if(!isset($form['subdir']))
			{
			$error['subdir']=1;
			}
		if(!(isset($form['lang']) && $form['lang']))
			{
			$error['lang']=1;
			}
		return $error;
		}
	}
