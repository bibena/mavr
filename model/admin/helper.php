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
		$yes_no_radio=array('show_error','full_error_info','log','advertisment');
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



/*****************************************************************************************************************
 * 
 * Functions to administrate menu
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/*****************************************************************************************************************
 * 
 * @return string content of admin panel for menu setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Menu_Display()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `menus` ORDER BY `sort`;";
			$query=$db->query($sql);
			$menus=$query->fetchAll(PDO::FETCH_ASSOC);
			$menu_content='';
			if($menus && is_array($menus))
				{
				foreach($menus as $menu)
					{
					if($menu["is_visible"])
						{
						$eye_status='open';
						$opacity='1';
						}
					else
						{
						$eye_status='close';
						$opacity='0.5';
						}
					$menu_content.='<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 ui-state-default admin_menu_item" style="opacity:'.$opacity.';">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
							<input class="form-control" name="'.$menu["sort"].'[path]" value="'.$menu["link"].'" disabled placeholder="Path">
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
							<input class="form-control" name="'.$menu["sort"].'[title]" value="'.$menu["title"].'" disabled required placeholder="Title">
						</div>
						<input name="'.$menu["sort"].'[is_visible]" type="hidden" value="'.$menu["is_visible"].'">
						<input name="'.$menu["sort"].'[id]" type="hidden" value="'.$menu["id"].'">
						<input name="'.$menu["sort"].'[delete]" type="hidden" value="0">
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
							<span class="glyphicon glyphicon-eye-'.$eye_status.'"></span>
						</div>
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
							<span class="glyphicon glyphicon-edit"></span>
						</div>
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
							<span class="glyphicon glyphicon-trash"></span>
						</div>
					</div>';
					}			
				}		
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $menu_content;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data fron admin panel
 * @return string content of admin panel for menu setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Menu_Save($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `menus` SET `sort`=:sort,`link`=:link,`title`=:title,`is_visible`=:is_visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `menus` (`sort`,`link`,`title`,`is_visible`) VALUES (:sort,:link,:title,:is_visible);";
			$delete_sql="DELETE FROM `menus` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(":sort"=>$sort,
									":link"=>trim(SUB_DIR.$value["path"],'/'),
									":title"=>$value["title"],
									":is_visible"=>$value["is_visible"]);
					if($value["id"]>0 && $value["id"]!=-1)
						{
						if($value["delete"])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(":id"=>$value["id"]);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[":id"]=$value["id"];
							}
						}
					else
						{
						$request=$db->prepare($insert_sql);
						}
					$request->execute($form_data);
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $this->Admin_Menu_Display();
		}



/*****************************************************************************************************************
 * 
 * Functions to administrate shop setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/*****************************************************************************************************************
 * 
 * @return string content of admin panel for shop countries setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Countries_Display()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `countries` ORDER BY `id`;";
			$query=$db->query($sql);
			$countries=$query->fetchAll(PDO::FETCH_ASSOC);
			$countries_content='';
			if($countries && is_array($countries))
				{
				$countries_content.='<form class="form-horizontal" role="form" method="post" action="'.SUB_DIR.'admin/shop">
				<div id="admin_shop_countries_list">';
				foreach($countries as $country)
					{
					if($country["is_visible"])
						{
						$eye_status='open';
						$opacity='1';
						}
					else
						{
						$eye_status='close';
						$opacity='0.5';
						}
					$countries_content.='<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 ui-state-default admin_shop_countries_item style="opacity:'.$opacity.';">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
						<input type="text" placeholder="Name" disabled="disabled" value="'.$country["country_name"].'" name="'.$country["id"].'[country_name]" class="form-control">
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
						<input type="text" placeholder="Phone code" required="required" disabled="disabled" value="'.$country["phone_code"].'" name="'.$country["id"].'[phone_code]" class="form-control">
					</div>
					<input type="hidden" value="'.$country["is_visible"].'" name="'.$country["id"].'[is_visible]">
					<input type="hidden" value="'.$country["id"].'" name="'.$country["id"].'[id]">
					<input type="hidden" value="0" name="'.$country["id"].'[delete]">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-eye-'.$eye_status.'"></span>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-edit"></span>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-trash"></span>
					</div>
				</div>';
					}
				$countries_content.='<input type="hidden" value="1" name="flag">
				<input type="hidden" value="countries" name="tab">				
				</div>
				<div class="admin_shop_country_add col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-3 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
					<button class="btn btn-info">Добавить</button>
				</div>
				<div class="admin_shop_country_save col-xs-2 col-sm-2 col-md-2 col-lg-2">	
					<button class="btn btn-success" type="submit">Сохранить</button>
				</div>
			</form>';
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $countries_content;
		}



/*****************************************************************************************************************
 * 
 * @return string content of admin panel for shop categories setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Categories_Display()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `categories` ORDER BY `sort`;";
			$query=$db->query($sql);
			$categories=$query->fetchAll(PDO::FETCH_ASSOC);
			$categories_content='';
			if($categories && is_array($categories))
				{
				$categories_content.='<form class="form-horizontal" role="form" method="post" action="'.SUB_DIR.'admin/shop" enctype="multipart/form-data">
				<div id="admin_shop_categories_list">';
				foreach($categories as $category)
					{
					if($category["is_visible"])
						{
						$eye_status='open';
						$opacity='1';
						}
					else
						{
						$eye_status='close';
						$opacity='0.5';
						}
					$categories_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_shop_categories_item" style="height:200px; opacity:'.$opacity.';">
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 input-group">
						<input type="text" placeholder="Name" required value="'.$category["category_name"].'" name="'.$category["sort"].'[category_name]" class="form-control">
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 input-group">
						<textarea required class="form-control" required name="'.$category["sort"].'[description]" rows="8">'.$category["description"].'</textarea>
					</div>
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 input-group">
						<input type="file" name="'.$category["sort"].'[image]">
					</div>
					<input type="hidden" value="'.$category["is_visible"].'" name="'.$category["sort"].'[is_visible]">
					<input type="hidden" value="'.$category["id"].'" name="'.$category["sort"].'[id]">
					<input type="hidden" value="0" name="'.$category["sort"].'[delete]">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-eye-'.$eye_status.'"></span>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-trash"></span>
					</div>
				</div>';
					}
				$categories_content.='<input type="hidden" value="1" name="flag">
				<input type="hidden" value="categories" name="tab">				
				</div>
				<div class="admin_shop_category_add col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-3 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
					<button class="btn btn-info">Добавить</button>
				</div>
				<div class="admin_shop_category_save col-xs-2 col-sm-2 col-md-2 col-lg-2">	
					<button class="btn btn-success" type="submit">Сохранить</button>
				</div>
			</form>';
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $categories_content;
		}



/*****************************************************************************************************************
 * 
 * @return string content of admin panel for shop manufacturers setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Manufacturers_Display()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `manufacturers` ORDER BY `manufacturer_name`;";
			$query=$db->query($sql);
			$manufacturers=$query->fetchAll(PDO::FETCH_ASSOC);
			$manufacturers_content='';
			if($manufacturers && is_array($manufacturers))
				{
				$manufacturers_content.='<form class="form-horizontal" role="form" method="post" action="'.SUB_DIR.'admin/shop" enctype="multipart/form-data">
				<div id="admin_shop_manufacturers_list">';
				foreach($manufacturers as $manufacturer)
					{
					if($manufacturer["is_visible"])
						{
						$eye_status='open';
						$opacity='1';
						}
					else
						{
						$eye_status='close';
						$opacity='0.5';
						}
					$manufacturers_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_shop_manufacturers_item" style="height:40px; opacity:'.$opacity.';">
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 input-group">
						<input type="text" placeholder="Name" required value="'.$manufacturer["manufacturer_name"].'" name="'.$manufacturer["id"].'[manufacturer_name]" class="form-control">
					</div>
					<!--<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 input-group">
						<textarea required class="form-control" required name="'.$manufacturer["id"].'[description]" rows="8">'.$manufacturer["id"].'</textarea>
					</div>
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 input-group">
						<input type="file" name="'.$manufacturer["id"].'[image]">
					</div>-->
					<input type="hidden" value="'.$manufacturer["is_visible"].'" name="'.$manufacturer["id"].'[is_visible]">
					<input type="hidden" value="'.$manufacturer["id"].'" name="'.$manufacturer["id"].'[id]">
					<input type="hidden" value="0" name="'.$manufacturer["id"].'[delete]">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-eye-'.$eye_status.'"></span>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-trash"></span>
					</div>
				</div>';
					}
				$manufacturers_content.='<input type="hidden" value="1" name="flag">
				<input type="hidden" value="manufacturers" name="tab">				
				</div>
				<div class="admin_shop_manufacturer_add col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-3 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
					<button class="btn btn-info">Добавить</button>
				</div>
				<div class="admin_shop_manufacturer_save col-xs-2 col-sm-2 col-md-2 col-lg-2">	
					<button class="btn btn-success" type="submit">Сохранить</button>
				</div>
			</form>';
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $manufacturers_content;
		}



/*****************************************************************************************************************
 * 
 * @return string content of admin panel for shop params setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Params_Display()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `params` ORDER BY `param_name`;";
			$query=$db->query($sql);
			$params=$query->fetchAll(PDO::FETCH_ASSOC);
			$params_content='';
			if($params && is_array($params))
				{
				$params_content.='<form class="form-horizontal" role="form" method="post" action="'.SUB_DIR.'admin/shop" enctype="multipart/form-data">
				<div id="admin_shop_params_list">';
				foreach($params as $param)
					{
					if($param["is_visible"])
						{
						$eye_status='open';
						$opacity='1';
						}
					else
						{
						$eye_status='close';
						$opacity='0.5';
						}
					$params_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_shop_params_item" style="height:40px; opacity:'.$opacity.';">
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 input-group">
						<input type="text" placeholder="Name" required value="'.$param["param_name"].'" name="'.$param["id"].'[param_name]" class="form-control">
					</div>
					<input type="hidden" value="'.$param["is_visible"].'" name="'.$param["id"].'[is_visible]">
					<input type="hidden" value="'.$param["id"].'" name="'.$param["id"].'[id]">
					<input type="hidden" value="0" name="'.$param["id"].'[delete]">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-eye-'.$eye_status.'"></span>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<span class="glyphicon glyphicon-trash"></span>
					</div>
				</div>';
					}
				$params_content.='<input type="hidden" value="1" name="flag">
				<input type="hidden" value="params" name="tab">				
				</div>
				<div class="admin_shop_param_add col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-3 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
					<button class="btn btn-info">Добавить</button>
				</div>
				<div class="admin_shop_param_save col-xs-2 col-sm-2 col-md-2 col-lg-2">	
					<button class="btn btn-success" type="submit">Сохранить</button>
				</div>
			</form>';
				}
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $params_content;
		}



/*****************************************************************************************************************
 * 
 * @return convert string content of admin panel for all shop setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Display()
		{
		try
			{
			$shop=array("categories"=>$this->Admin_Shop_Categories_Display(),
						"manufacturers"=>$this->Admin_Shop_Manufacturers_Display(),
						"params"=>$this->Admin_Shop_Params_Display(),
						"countries"=>$this->Admin_Shop_Countries_Display()
						);
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $shop;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Countries_Save($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `countries` SET `country_name`=:country_name,`phone_code`=:phone_code,`is_visible`=:is_visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `countries` (`country_name`,`phone_code`,`is_visible`) VALUES (:country_name,:phone_code,:is_visible);";
			$delete_sql="DELETE FROM `countries` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(":country_name"=>$value["country_name"],
									":phone_code"=>$value["phone_code"],
									":is_visible"=>$value["is_visible"]);
					if($value["id"]>0 && $value["id"]!=-1)
						{
						if($value["delete"])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(":id"=>$value["id"]);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[":id"]=$value["id"];
							}
						}
					else
						{
						$request=$db->prepare($insert_sql);
						}
					$request->execute($form_data);
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Categories_Save($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `categories` SET `category_name`=:category_name,`sort`=:sort,`is_visible`=:is_visible,`description`=:description WHERE `id`=:id;";
			$insert_sql="INSERT INTO `categories` (`category_name`,`sort`,`is_visible`,`description`) VALUES (:category_name,:sort,:is_visible,:description);";
			$delete_sql="DELETE FROM `categories` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(":category_name"=>$value["category_name"],
									":sort"=>$sort,
									":is_visible"=>$value["is_visible"],
									":description"=>$value["description"]);
					if($value["id"]>0 && $value["id"]!=-1)
						{
						if($value["delete"])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(":id"=>$value["id"]);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[":id"]=$value["id"];
							$category_id=$value["id"];
							}
						}
					else
						{
						$request=$db->prepare($insert_sql);
						}
					$request->execute($form_data);
					if($value["id"]==-1)
						{
						$category_id=$db->lastInsertId();
						}
					if($form["postfiles"][$sort]["name"]["image"])
						{
						$path=call_user_func('end',explode('/',$form["postfiles"][$sort]["tmp_name"]["image"]));
						$image_insert_sql="INSERT INTO `images` (`image_name`,`path`,`is_visible`) VALUES (:name,:path,:is_visible);";
						$request=$db->prepare($image_insert_sql);
						$request->execute(array(
									":name"=>$form["postfiles"][$sort]["name"]["image"],
									":path"=>$path,
									":is_visible"=>1));
						$new_image_id=$db->lastInsertId();
						$category_update_sql="UPDATE `categories` SET `image_id`=:image_id WHERE `id`=:id;";
						$request=$db->prepare($category_update_sql);
						$request->execute(array(
									":image_id"=>$new_image_id,
									":id"=>$category_id));
						if(!file_exists(ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id))
							{
							mkdir(ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id);
							}
						rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id.DS.$path);
						}					
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Manufacturers_Save($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `manufacturers` SET `manufacturer_name`=:manufacturer_name,`is_visible`=:is_visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `manufacturers` (`manufacturer_name`,`is_visible`) VALUES (:manufacturer_name,:is_visible);";
			$delete_sql="DELETE FROM `manufacturers` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(":manufacturer_name"=>$value["manufacturer_name"],
									":is_visible"=>$value["is_visible"]);
					if($value["id"]>0 && $value["id"]!=-1)
						{
						if($value["delete"])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(":id"=>$value["id"]);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[":id"]=$value["id"];
							}
						}
					else
						{
						$request=$db->prepare($insert_sql);
						}
					$request->execute($form_data);
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Params_Save($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `params` SET `param_name`=:param_name,`is_visible`=:is_visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `params` (`param_name`,`is_visible`) VALUES (:param_name,:is_visible);";
			$delete_sql="DELETE FROM `params` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(":param_name"=>$value["param_name"],
									":is_visible"=>$value["is_visible"]);
					if($value["id"]>0 && $value["id"]!=-1)
						{
						if($value["delete"])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(":id"=>$value["id"]);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[":id"]=$value["id"];
							}
						}
					else
						{
						$request=$db->prepare($insert_sql);
						}
					$request->execute($form_data);
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return convert string content of admin panel for all shop setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function Admin_Shop_Save($form)
		{
		try 
			{
			switch($form["tab"])
				{
				case 'categories':
					$this->Admin_Shop_Categories_Save($form);
					break;
				case 'manufacturers':
					$this->Admin_Shop_Manufacturers_Save($form);
					break;
				case 'params':
					$this->Admin_Shop_Params_Save($form);
					break;
				case 'countries':
					$this->Admin_Shop_Countries_Save($form);
					break;
				default:
					break;
				}
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $this->Admin_Shop_Display();
		}







	public function Get_Products()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT `p`.`id`,`p`.`description`,`p`.`product_name`,`p`.`is_visible`,`pi`.`path` FROM `products` AS `p` LEFT JOIN (SELECT * FROM `products_images` JOIN `images` ON `products_images`.`image_id`=`images`.`id` WHERE `images`.`is_visible`='1' ORDER BY `products_images`.`sort`) as `pi` ON `p`.`id`=`pi`.`product_id` GROUP BY `p`.`id` HAVING min(`p`.`id`);";
			$request=$db->prepare($sql);
			$request->execute();
			$products=$request->fetchAll();
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $products;
		}



	public function Display_Products()
		{
		try
			{
			$products_content='';
			foreach($this->Get_Products() as $product)
				{
				if($product["path"]=='')
					{
					$image_name='no_image.png';
					}
				else
					{
					$image_name='products'.DS.$product["id"].DS.$product["path"];
					}
				$products_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_products_item';
				if(!$product["is_visible"])
					{
					$products_content.='" style="opacity: 0.5;';
					}
				$products_content.='">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a class="link" href="'.SUB_DIR.'admin/product'.DS.$product["id"].'"><img style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.$image_name.'"></a>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a class="link" href="'.SUB_DIR.'admin/product'.DS.$product["id"].'">'.$product["product_name"].'</a>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="line-height:inherit;text-align:left;">
					<span>'.mb_substr(/*strip_tags(*/$product["description"]/*)*/,0,200,'UTF-8').'&hellip;</span>
				</div>
				<input type="hidden" value="'.$product["is_visible"].'" name="'.$product["id"].'[visible]">
				<input type="hidden" value="0" name="'.$product["id"].'[delete]">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-eye-';
				if($product["is_visible"])
					{
					$products_content.='open';
					}
				else
					{
					$products_content.='close';
					}
				$products_content.='"></span>
				</div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-trash"></span>
				</div>
			</div>';
				}
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $products_content;
		}



	function Check_Products($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `products` SET `is_visible`=:visible WHERE `id`=:id;";
			$delete_sql="DELETE FROM `products` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $id=>$value)
				{
				if(is_numeric($id))
					{
					if($value['delete'])
						{
						$sql="DELETE FROM `categories_products` WHERE `product_id`=:id;";
						$form_data=array(':id'=>$id);
						$request=$db->prepare($sql);
						$request->execute($form_data);
						$sql="DELETE FROM `products_images` WHERE `product_id`=:id;";
						$form_data=array(':id'=>$id);
						$request=$db->prepare($sql);
						$request->execute($form_data);
						$sql="DELETE FROM `products_params` WHERE `product_id`=:id;";
						$form_data=array(':id'=>$id);
						$request=$db->prepare($sql);
						$request->execute($form_data);
						$request=$db->prepare($delete_sql);
						}
					else
						{
						$request=$db->prepare($update_sql);
						$form_data=array(':visible'=>$value['visible'],':id'=>$id);
						}
					$request->execute($form_data);
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $this->Display_Products();
		}



	public function Get_Product($id)
		{
		try
			{
			$db=Db::Get_Instance();
			$request_array=array(':id'=>$id);
			$sql="SELECT `p`.`manufacturer_id` , `p`.`product_name` , `p`.`description` , `p`.`price` , `p`.`amount` , `p`.`is_visible` FROM `products` AS `p` WHERE `p`.`id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product=$request->fetchAll(PDO::FETCH_ASSOC);
			$product=$product[0];
			
			$sql="SELECT `i`.`id`,`i`.`image_name`,`i`.`path`,`pi`.`sort`,`i`.`is_visible` FROM `products_images` as `pi` LEFT JOIN `images` as `i` ON `pi`.`image_id`=`i`.`id`  WHERE `pi`.`product_id`=:id ORDER BY `pi`.`sort` ASC;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['images']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT `c`.`id` FROM `categories_products` AS `pc` LEFT JOIN `categories` as `c` ON `c`.`id`=`pc`.`category_id` WHERE `pc`.`product_id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['categories']=$request->fetchAll(PDO::FETCH_ASSOC);

			$sql="SELECT `p`.`id`,`p`.`param_name`,`pp`.`value` FROM `products_params` AS `pp` LEFT JOIN `params` as `p` ON `p`.`id`=`pp`.`param_id` WHERE `pp`.`product_id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['params']=$request->fetchAll(PDO::FETCH_ASSOC);
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $product;
		}



	public function Get_Product_General()
		{
		try
			{
			$db=Db::Get_Instance();
			
			$sql="SELECT * FROM `categories`;";
			$request=$db->prepare($sql);
			$request->execute();
			$general['all_categories']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT * FROM `manufacturers`;";
			$request=$db->prepare($sql);
			$request->execute();
			$general['manufacturers']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT * FROM `params`;";
			$request=$db->prepare($sql);
			$request->execute();
			$general['all_params']=$request->fetchAll(PDO::FETCH_ASSOC);
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $general;
		}



	private function Display_Product_General(array $product_source,$id)
		{
		try
			{
			if($id==-1)
				{
				$product_source["is_visible"]=1;
				$product_source["product_name"]='';
				$product_source["manufacturer_id"]=0;
				$product_source["categories"]=array();
				$product_source["price"]='';
				$product_source["amount"]='';
				$product_source["description"]='';
				}
			$general_content='<input type="hidden" name="id" value="'.$id.'">
<div class="form-group">
	<label for="product_visible" class="col-sm-2 control-label">Visible</label>
	<div class="col-sm-10">
		<label class="checkbox-inline">
			<input type="checkbox" name="visible" value="1"';
			$general_content.=($product_source["is_visible"])?' checked':'';
			$general_content.=' class="form-control" id="product_visible">
		</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Name</label>
	<div class="col-sm-10">
		<input required class="form-control" name="name" value="'.$product_source["product_name"].'" placeholder="Name">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Manufacturer</label>
	<div class="col-sm-10">
		<select required name="manufacturer" class="form-control">';
			foreach($product_source["manufacturers"] as $manufacturer)
				{
				$general_content.='<option value="'.$manufacturer["id"].'"';
				$general_content.=($product_source["manufacturer_id"]==$manufacturer["id"])?' selected':'';
				$general_content.='>'.$manufacturer["manufacturer_name"].'</option>';
				}
			$general_content.='</select>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Categories</label>
	<div class="col-sm-10">
		<select required size="3" name="categories[]" multiple class="form-control">';
			foreach($product_source["all_categories"] as $all_categories)
				{
				$general_content.='<option value="'.$all_categories["id"].'"';
				foreach($product_source["categories"] as $categories)
					{
					$general_content.=($all_categories["id"]==$categories["id"])?' selected':'';					
					}
				$general_content.='>'.$all_categories["category_name"].'</option>';
				}
			$general_content.='</select>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Price</label>
	<div class="col-sm-10">
		<input type="number" value="'.$product_source["price"].'" name="price" class="form-control" placeholder="Price">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Amount</label>
	<div class="col-sm-10">
		<input required type="number" value="'.$product_source["amount"].'" name="amount" class="form-control" placeholder="Amount">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Description</label>
	<div class="col-sm-10">
		<textarea required class="form-control" name="description" rows="10">'.$product_source["description"].'</textarea>
	</div>
			</div>';
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $general_content;
		}



	public function Display_All_Params(array $product_source)
		{
		try
			{
			$option_html='';
			foreach($product_source["all_params"] as $all_param)
				{
				$option_html.='<option value="'.$all_param["id"].'">'.$all_param["param_name"].'</option>';
				}
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $option_html;
		}



	public function Display_Product_Params(array $product_source,$id)
		{
		try
			{
			$params_content='';
			foreach($product_source["params"] as $i=>$param)
				{
				$params_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 product_params_item">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
					<select required name="params['.$i.'][params]" class="form-control">';
						foreach($product_source["all_params"] as $all_param)
							{
							$params_content.='<option value="'.$all_param["id"].'"';
							$params_content.=($all_param["id"]==$param["id"])?' selected':'';
							$params_content.='>'.$all_param["param_name"].'</option>';
							}
						$params_content.='</select>
				</div>
				<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 input-group">
					<input type="text" placeholder="Param value" required="required" value="'.htmlentities($param["value"]).'" name="params['.$i.'][value]" class="form-control">
				</div>
				<input type="hidden" value="0" name="params['.$i.'][new]">
				<input type="hidden" value="0" name="params['.$i.'][delete]">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-trash"></span>
				</div>
			</div>';
				}
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return array('content'=>$params_content,'options'=>$this->Display_All_Params($product_source));
		}



	public function Display_Product_Images(array $product_source,$id)
		{
		try
			{
			$image_content='';
			foreach($product_source["images"] as $image)
				{
				if($image["is_visible"]==1)
					{
					$visible='';
					$eye='open';
					}
				else
					{
					$visible='style="opacity:0.5" ';
					$eye='close';
					}
				$image_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default product_images_item" '.$visible.'>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<img alt="'.$image["image_name"].'" style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.'products'.DS.$id.DS.$image["path"].'">
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 input-group">
					<input type="file" name="images['.$image["sort"].']" class="form-control">
				</div>
				<input type="hidden" value="'.$image["id"].'" name="imagesdata['.$image["sort"].'][id]">
				<input type="hidden" value="'.$image["is_visible"].'" name="imagesdata['.$image["sort"].'][visible]">
				<input type="hidden" value="0" name="imagesdata['.$image["sort"].'][delete]">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-eye-'.$eye.'"></span>
				</div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-trash"></span>
				</div>
			</div>';
				}
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $image_content;
		}



	public function Display_Product($id)
		{
		try
			{
			$product_source=array_merge($this->Get_Product($id),$this->Get_Product_General());
			$product_params=$this->Display_Product_Params($product_source,$id);
			$products_content=array('id'=>$id,
									'options'=>$product_params['options'],
									'general'=>$this->Display_Product_General($product_source,$id),
									'params'=>$product_params['content'],
									'images'=>$this->Display_Product_Images($product_source,$id));
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $products_content;
		}



	public function Check_Product($form)
		{
		try
			{
			$source=$this->Get_Product($form['id']);
			$db=Db::Get_Instance();
			$db->beginTransaction();
//---update name,description,price,amount,visibility,modification_time
			$form_data=array();
			$sql="UPDATE `products` SET";
			if($source['product_name']!=$form['name'])
				{
				$sql.=" `product_name`=:name,";
				$form_data[':name']=$form['name'];
				}
			if($source['description']!=$form['description'])
				{
				$sql.=" `description`=:description,";
				$form_data[':description']=$form['description'];
				}
			if($source['price']!=$form['price'])
				{
				$sql.=" `price`=:price,";
				$form_data[':price']=$form['price'];
				}
			if($source['amount']!=$form['amount'])
				{
				$sql.=" `amount`=:amount,";
				$form_data[':amount']=$form['amount'];
				}
			if($source['manufacturer_id']!=$form['manufacturer'])
				{
				$sql=" `manufacturer_id`=:manufacturer,";
				$form_data[':manufacturer']=$form['manufacturer'];
				}
			if($source['is_visible']!=$form['visible'])
				{
				$sql.=" `is_visible`=:visible,";
				$form_data[':visible']=$form['visible'];
				}
			if(strlen($sql)>21)
				{
				$sql=substr($sql,0,-1);
				$sql.=" WHERE `id`=:id;";
				$form_data[':id']=$form['id'];
				$request=$db->prepare($sql);
				$request->execute($form_data);
				}
//---update categories
			$categories=array();
			foreach($source['categories'] as $v)
				{
				$categories[]=$v['id'];
				}
			$deleted_categories=array_diff($categories,$form['categories']);
			$added_categories=array_diff($form['categories'],$categories);
			foreach($deleted_categories as $dc)
				{
				$form_data=array(':id'=>$form['id'],':category'=>$dc);
				$sql="DELETE FROM `categories_products` WHERE `product_id`=:id AND `category_id`=:category;";
				$request=$db->prepare($sql);
				$request->execute($form_data);				
				}
			foreach($added_categories as $dc)
				{
				$form_data=array(':id'=>$form['id'],':category'=>$dc);
				$sql="INSERT INTO `categories_products` (`product_id`,`category_id`) VALUES (:id,:category);";
				$request=$db->prepare($sql);
				$request->execute($form_data);
				}
//---update params
			$update_sql="UPDATE `products_params` SET `value`=:value WHERE `product_id`=:id AND `param_id`=:param;";
			$insert_sql="INSERT INTO `products_params` (`product_id`,`param_id`,`value`) VALUES (:id,:param,:value);";
			$delete_sql="DELETE FROM `products_params` WHERE `product_id`=:id AND `param_id`=:param;";
			foreach($form['params'] as $param)
				{
				$form_data=array(':id'=>$form['id'],':param'=>$param['params']);
				if($param['new'])
					{
					$request=$db->prepare($insert_sql);
					$form_data[':value']=$param['value'];
					}
				else
					{
					if($param['delete'])
						{
						$request=$db->prepare($delete_sql);
						}
					else
						{
						$request=$db->prepare($update_sql);
						$form_data[':value']=$param['value'];
						}
					}
				$request->execute($form_data);
				}
//---update images
			if(isset($form['postfiles']) && isset($form['imagesdata']))
				{
				foreach($form['imagesdata'] as $sortid=>$imagedata)
					{
					if($imagedata['id']=='-1')
						{
						if(strpos($form['postfiles']['images']['type'][$sortid],'image')!==false)
							{
							$path=call_user_func('end',explode('/',$form['postfiles']['images']['tmp_name'][$sortid]));
							$sql="INSERT INTO `images` (`image_name`,`path`,`is_visible`) VALUES (:name,:path,:visible);";
							$request=$db->prepare($sql);
							$request->execute(array(':name'=>$form['postfiles']['images']['name'][$sortid],':path'=>$path,':visible'=>$imagedata['visible']));
							$lastid=$db->lastInsertId();
							if(!file_exists(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']))
								{
								mkdir(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']);
								}
							rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id'].DS.$path);
							$sql="INSERT INTO `products_images` (`image_id`,`product_id`,`sort`) VALUES ('".$lastid."','".$form['id']."','".$sortid."');";
							$db->exec($sql);
							}
						}
					else
						{
						$form_data=array(':id'=>$imagedata['id']);
						if($imagedata['delete'])
							{
							$sql="DELETE FROM `images` WHERE `id`='".$imagedata['id']."';";
							$db->exec($sql);
							$sql="DELETE FROM `products_images` WHERE `image_id`='".$imagedata['id']."';";
							$db->exec($sql);
							
							$request=$db->prepare($delete_sql);
							}
						else
							{
							$sql="UPDATE `products_images` SET `sort`='".$sortid."' WHERE `image_id`='".$imagedata['id']."';";
							$db->exec($sql);
							$sql="UPDATE `images` SET `is_visible`='".$imagedata['visible']."' WHERE `id`='".$imagedata['id']."';";
							$db->exec($sql);
							}
						}
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		return $this->Display_Product($form['id']);
		}


	public function Add_Product()
		{
		try
			{
			$id=-1;			
			$product_source=$this->Get_Product_General();
			$products_content=array('id'=>$id,
									'options'=>$this->Display_All_Params($product_source),
									'general'=>$this->Display_Product_General($product_source,$id),
									'params'=>'',
									'images'=>'');
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $products_content;
		}



	public function Check_New_Product($form)
		{
		try
			{
			$db=Db::Get_Instance();
			$db->beginTransaction();
//---prod
			$sql="INSERT INTO `products` (`manufacturer_id`,`product_name`,`description`,`price`,`amount`,`is_visible`) VALUES (:manufacturer,:product_name,:description,:price,:amount,:visible);";
			$request_array=array(':manufacturer'=>$form['manufacturer'],':product_name'=>$form['name'],':description'=>$form['description'],':price'=>$form['price'],':amount'=>$form['amount'],':visible'=>$form['visible']);
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['params']=$request->fetchAll(PDO::FETCH_ASSOC);
			$lastid=$db->lastInsertId();
//---cat
			$sql="INSERT INTO `categories_products` (`product_id`,`category_id`) VALUES (:id,:category);";
			foreach($form['categories'] as $cat)
				{
				$form_data=array(':id'=>$lastid,':category'=>$cat);
				$request=$db->prepare($sql);
				$request->execute($form_data);
				}
//---param
			if(isset($form['params']) && is_array($form['params']))
				{
				$sql="INSERT INTO `products_params` (`product_id`,`param_id`,`value`) VALUES (:id,:param,:value);";
				foreach($form['params'] as $param)
					{
					$form_data=array(':id'=>$lastid,':param'=>$param['params'],':value'=>$param['value']);
					$request=$db->prepare($sql);
					$request->execute($form_data);
					}
				}
//---img
			if(isset($form['postfiles']) && isset($form['imagesdata']))
				{
				foreach($form['imagesdata'] as $sortid=>$imagedata)
					{
					if($imagedata['id']=='-1')
						{
						if(strpos($form['postfiles']['images']['type'][$sortid],'image')!==false)
							{
							$path=call_user_func('end',explode('/',$form['postfiles']['images']['tmp_name'][$sortid]));
							$sql="INSERT INTO `images` (`image_name`,`path`,`is_visible`) VALUES (:name,:path,:visible);";
							$request=$db->prepare($sql);
							$request->execute(array(':name'=>$form['postfiles']['images']['name'][$sortid],':path'=>$path,':visible'=>$imagedata['visible']));
							$lastim=$db->lastInsertId();
							if(!file_exists(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']))
								{
								mkdir(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']);
								}
							rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id'].DS.$path);
							$sql="INSERT INTO `products_images` (`image_id`,`product_id`,`sort`) VALUES ('".$lastim."','".$lastid."','".$sortid."');";
							$db->exec($sql);
							}
						}
					}
				}
			$db->commit();
			}
		catch (Db_Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		catch (Error $e) 
			{
			$db->rollBack();
			$e->Error();
			}
		return $this->Display_Product($lastid);
		}
	}
