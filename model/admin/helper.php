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
	function Display_Menu()
		{
		$db=Db::Get_Instance();
		$sql="SELECT * FROM `menus` ORDER BY `sort`;";
		$request=$db->prepare($sql);
		$request->execute();
		$menus=$request->fetchAll();
		$content='';
		foreach($menus as $menu)
			{
			$trash=Html::Tag('div',array('class'=>'col-xs-1 col-sm-1 col-md-1 col-lg-1'),Html::Tag('span',array('class'=>'glyphicon glyphicon-trash')));
			$edit=Html::Tag('div',array('class'=>'col-xs-1 col-sm-1 col-md-1 col-lg-1'),Html::Tag('span',array('class'=>'glyphicon glyphicon-edit')));
			$link=Html::Tag('div',array('class'=>'col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group'),Html::Tag('input',array('class'=>'form-control','name'=>$menu['sort'].'[path]','type'=>'text','value'=>$menu['link'],'disabled'=>'disabled','placeholder'=>'Path')));
			$title=Html::Tag('div',array('class'=>'col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group'),Html::Tag('input',array('class'=>'form-control','name'=>$menu['sort'].'[title]','type'=>'text','value'=>$menu['title'],'disabled'=>'disabled','required'=>'required','placeholder'=>'Title')));
			$hvisible=Html::Tag('input',array('name'=>$menu['sort'].'[visible]','type'=>'hidden','value'=>$menu['is_visible']));
			$id=Html::Tag('input',array('name'=>$menu['sort'].'[id]','type'=>'hidden','value'=>$menu['id']));
			$delete=Html::Tag('input',array('name'=>$menu['sort'].'[delete]','type'=>'hidden','value'=>0));
			if($menu['is_visible'])
				{
				$visible=Html::Tag('div',array('class'=>'col-xs-1 col-sm-1 col-md-1 col-lg-1'),Html::Tag('span',array('class'=>'glyphicon glyphicon-eye-open')));
				$content.=Html::Tag('div',array('class'=>'col-xs-11 col-sm-11 col-md-11 col-lg-11 ui-state-default admin_menu_item'),$link.$title.$hvisible.$id.$delete.$visible.$edit.$trash);
				}
			else
				{
				$visible=Html::Tag('div',array('class'=>'col-xs-1 col-sm-1 col-md-1 col-lg-1'),Html::Tag('span',array('class'=>'glyphicon glyphicon-eye-close')));
				$content.=Html::Tag('div',array('class'=>'col-xs-11 col-sm-11 col-md-11 col-lg-11 ui-state-default ui-state-disabled admin_menu_item','style'=>'opacity: 0.5;'),$link.$title.$hvisible.$id.$delete.$visible.$edit.$trash);
				}
			}
		return $content;
		}
	function Check_Menu($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `menus` SET `sort`=:sort,`link`=:link,`title`=:title,`is_visible`=:visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `menus` (`sort`,`link`,`title`,`is_visible`) VALUES (:sort,:link,:title,:visible);";
			$delete_sql="DELETE FROM `menus` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					$form_data=array(':sort'=>$sort,':link'=>trim(SUB_DIR.$value['path'],'/'),':title'=>$value['title'],':visible'=>$value['visible']);
					if($value['id']>0 && $value['id']!=-1)
						{
						if($value['delete'])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(':id'=>$value['id']);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[':id']=$value['id'];
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
		return $this->Display_Menu();
		}
	public function Get_Countries()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT * FROM `countries`;";
			$request=$db->prepare($sql);
			$request->execute();
			$countries=$request->fetchAll();
			}
		catch (Db_Error $e) 
			{
			$e->Error();
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $countries;
		}
	public function Display_Countries()
		{
		try
			{
			$countries_content='';
			foreach($this->Get_Countries() as $country)
				{
				$countries_content.='<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 ui-state-default admin_countries_item';
				if(!$country["is_visible"])
					{
					$countries_content.='" style="opacity: 0.5;';
					}
				$countries_content.='">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
					<input type="text" placeholder="Name" disabled="disabled" value="'.$country["name"].'" name="'.$country["id"].'[name]" class="form-control">
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
					<input type="text" placeholder="Phone code" required="required" disabled="disabled" value="'.$country["phone_code"].'" name="'.$country["id"].'[phone_code]" class="form-control">
				</div>
				<input type="hidden" value="'.$country["is_visible"].'" name="'.$country["id"].'[visible]">
				<input type="hidden" value="0" name="'.$country["id"].'[new]">
				<input type="hidden" value="0" name="'.$country["id"].'[delete]">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<span class="glyphicon glyphicon-eye-';
				if($country["is_visible"])
					{
					$countries_content.='open';
					}
				else
					{
					$countries_content.='close';
					}
				$countries_content.='"></span>
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
		catch (Error $e) 
			{
			$e->Error();
			}
		return $countries_content;
		}
	public function Display_Shop()
		{
		try
			{
			$shop=array('countries'=>$this->Display_Countries(),'cities'=>'','shipment'=>'','payment'=>'');
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $shop;
		}
	function Check_Shop($form)
		{
		try 
			{
			$db=Db::Get_Instance();
			$update_sql="UPDATE `countries` SET `name`=:name,`phone_code`=:phone_code,`is_visible`=:visible WHERE `id`=:id;";
			$insert_sql="INSERT INTO `countries` (`name`,`phone_code`,`is_visible`) VALUES (:name,:phone_code,:visible);";
			$delete_sql="DELETE FROM `countries` WHERE `id`=:id;";
			$db->beginTransaction();
			foreach($form as $id=>$value)
				{
				if(is_numeric($id))
					{
					$form_data=array(':name'=>trim(SUB_DIR.$value['name'],'/'),':phone_code'=>$value['phone_code'],':visible'=>$value['visible']);
					if($value['new'])
						{
						$request=$db->prepare($insert_sql);
						}
					else
						{
						if($value['delete'])
							{
							$request=$db->prepare($delete_sql);
							$form_data=array(':id'=>$id);
							}
						else
							{
							$request=$db->prepare($update_sql);
							$form_data[':id']=$id;
							}
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
		return $this->Display_Shop();
		}


	public function Get_Products()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT `p`.`id`,`p`.`description`,`p`.`name`,`p`.`is_visible`,`i`.`alias` FROM `products` AS 'p' LEFT JOIN `products_images` as 'pi' ON `p`.`id`=`pi`.`product_id` LEFT JOIN `images` as 'i' ON `pi`.`image_id`=`i`.`id`  GROUP BY `p`.`id` HAVING min('p'.'id');";
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
				$product["alias"].=($product["alias"]=='')?'1.png':'';
				$products_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_products_item';
				if(!$product["is_visible"])
					{
					$products_content.='" style="opacity: 0.5;';
					}
				$products_content.='">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a href="'.SUB_DIR.'admin/product'.DS.$product["id"].'"><img style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.$product["alias"].'"></a>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a href="'.SUB_DIR.'admin/product'.DS.$product["id"].'">'.$product["name"].'</a>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="line-height:inherit;text-align:left;">
					<span>'.mb_substr(strip_tags($product["description"]),0,200,'UTF-8').'&hellip;</span>
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
						$request=$db->prepare($delete_sql);
						$form_data=array(':id'=>$id);
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
	}
