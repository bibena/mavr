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
			$sql="SELECT `p`.`id`,`p`.`description`,`p`.`name`,`p`.`is_visible`,`i`.`path` AS `image_path` FROM `products` AS 'p' LEFT JOIN `products_images` as 'pi' ON `p`.`id`=`pi`.`product_id` LEFT JOIN `images` as 'i' ON `pi`.`image_id`=`i`.`id` GROUP BY `p`.`id` HAVING min('p'.'id');";
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
				if($product["image_path"]=='')
					{
					$image_name='no_image.png';
					}
				else
					{
					$image_name='products'.DS.$product["id"].DS.$product["image_path"];
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
					<a class="link" href="'.SUB_DIR.'admin/product'.DS.$product["id"].'">'.$product["name"].'</a>
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



	public function Get_Product($id)
		{
		try
			{
			$db=Db::Get_Instance();
			$request_array=array(':id'=>$id);
			$sql="SELECT `p`.`manufacturer_id`,`p`.`name`,`p`.`description`,`p`.`price`,`p`.`amount`,`p`.`is_visible` FROM `products` AS 'p' WHERE `p`.`id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product=$request->fetchAll(PDO::FETCH_ASSOC);
			$product=$product[0];
			
			$sql="SELECT `i`.`id`,`i`.`name`,`i`.`path`,`pi`.`sort`,`pi`.`is_visible` FROM `products_images` as 'pi' LEFT JOIN `images` as 'i' ON `pi`.`image_id`=`i`.`id`  WHERE `pi`.`product_id`=:id ORDER BY `pi`.`sort` ASC;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['images']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT `c`.`id` FROM `products_categories` AS 'pc' LEFT JOIN `categories` as 'c' ON `c`.`id`=`pc`.`category_id` WHERE `pc`.`product_id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['categories']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT * FROM `categories`;";
			$request=$db->prepare($sql);
			$request->execute();
			$product['all_categories']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT * FROM `manufacturers`;";
			$request=$db->prepare($sql);
			$request->execute();
			$product['manufacturers']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT `p`.`id`,`p`.`name`,`pp`.`value` FROM `products_params` AS 'pp' LEFT JOIN `params` as 'p' ON `p`.`id`=`pp`.`param_id` WHERE `pp`.`product_id`=:id;";
			$request=$db->prepare($sql);
			$request->execute($request_array);
			$product['params']=$request->fetchAll(PDO::FETCH_ASSOC);
			
			$sql="SELECT * FROM `params`;";
			$request=$db->prepare($sql);
			$request->execute();
			$product['all_params']=$request->fetchAll(PDO::FETCH_ASSOC);
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



	private function Display_Product_General(array $product_source,$id)
		{
		try
			{
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
		<input required class="form-control" name="name" value="'.$product_source["name"].'" placeholder="Name">
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
				$general_content.='>'.$manufacturer["name"].'</option>';
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
				$general_content.='>'.$all_categories["name"].'</option>';
				}
			$general_content.='</select>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Price</label>
	<div class="col-sm-10">
		<input required type="number" value="'.$product_source["price"].'" name="price" class="form-control" placeholder="Price">
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



	public function Display_Product_Params(array $product_source,$id)
		{
		try
			{
			$params_content=$option_html='';
			foreach($product_source["params"] as $i=>$param)
				{
				$option_html='';
				$params_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 product_params_item">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
					<select required name="params['.$i.'][params]" class="form-control">';
						foreach($product_source["all_params"] as $all_param)
							{
							$params_content.='<option value="'.$all_param["id"].'"';
							$params_content.=($all_param["id"]==$param["id"])?' selected':'';
							$params_content.='>'.$all_param["name"].'</option>';
							$option_html.='<option value="'.$all_param["id"].'">'.$all_param["name"].'</option>';
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
		return array('content'=>$params_content,'options'=>$option_html);
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
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 input-group">
					<img alt="'.$image["name"].'" style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.'products'.DS.$id.DS.$image["path"].'">
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
			$product_source=$this->Get_Product($id);
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
			if($source['name']!=$form['name'])
				{
				$sql.=" `name`=:name,";
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
			if($source['is_visible']!=$form['visible'])
				{
				$sql.=" `is_visible`=:visible,";
				$form_data[':visible']=$form['visible'];
				}
			$sql.=" `time_of_modifying`='".time()."' WHERE `id`=:id;";
			$form_data[':id']=$form['id'];
			$request=$db->prepare($sql);
			$request->execute($form_data);
//---update manufacturer_id
			if($source['manufacturer_id']!=$form['manufacturer'])
				{
				$sql="UPDATE `products` SET `manufacturer_id`='".$form['manufacturer']."';";
				$db->exec($sql);
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
				$sql="DELETE FROM `products_categories` WHERE `product_id`=:id AND `category_id`=:category;";
				$request=$db->prepare($sql);
				$request->execute($form_data);				
				}
			foreach($added_categories as $dc)
				{
				$form_data=array(':id'=>$form['id'],':category'=>$dc);
				$sql="INSERT INTO `products_categories` (`product_id`,`category_id`) VALUES (:id,:category);";
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
					$update_sql="UPDATE `products_images` SET `value`=:value WHERE `product_id`=:id AND `param_id`=:param;";
					foreach($form['imagesdata'] as $sortid=>$imagedata)
						{
						if($imagedata['id']=='-1')
							{
							if(strpos($form['postfiles']['images']['type'][$sortid],'image')!==false)
								{
								$path=call_user_func('end',explode('/',$form['postfiles']['images']['tmp_name'][$sortid]));
								$sql="INSERT INTO `images` (`name`,`path`) VALUES (:name,:path);";
								$request=$db->prepare($sql);
								$request->execute(array(':name'=>$form['postfiles']['images']['name'][$sortid],':path'=>$path));
								$lastid=$db->lastInsertId();
								if(!file_exists(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']))
									{
									mkdir(ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id']);
									}
								rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'products'.DS.$form['id'].DS.$path);
								$sql="INSERT INTO `products_images` (`image_id`,`product_id`,`sort`,`is_visible`) VALUES ('".$lastid."','".$form['id']."','".$sortid."','".$imagedata['visible']."');";
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
								$sql="UPDATE;";
								}
							$request->execute($form_data);
							}
						}
					}
//var_dump($form_data);
//+visible




/*
$form['postfiles']
array(1) {
 ["images"]=> array(5) 
{
 ["name"]=> array(2) { [0]=> string(6) "3.jpeg" [2]=> string(6) "9.jpeg" } 
 ["type"]=> array(2) { [0]=> string(10) "image/jpeg" [2]=> string(10) "image/jpeg" } 
 ["tmp_name"]=> array(2) { [0]=> string(45) "/media/DISK_A1/web/temporary/upload/phphpot2v" [2]=> string(45) "/media/DISK_A1/web/temporary/upload/php2PUeXR" } 
 ["error"]=> array(2) { [0]=> int(0) [2]=> int(0) } 
 ["size"]=> array(2) { [0]=> int(256174) [2]=> int(246156) } 
 } 
 }

$form['imagesdata']
 array(2) 
 {
  [0]=> array(2) { ["id"]=> string(1) "-1" ["delete"]=> string(1) "0" } 
  [2]=> array(2) { ["id"]=> string(1) "-1" ["delete"]=> string(1) "0" } 
 } 



/*
array(10) { 
["id"]=> string(1) "3" 
["visible"]=> string(1) "1" 
["name"]=> string(1) "2" 
["manufacturer"]=> string(1) "1" 
["categories"]=> array(1) { [0]=> string(1) "1" } 
["price"]=> string(1) "4" 
["amount"]=> string(1) "5" 
["description"]=> string(1) "3" 
["params"]=> array(1) {
 [0]=> array(4) {
  ["params"]=> string(1) "3"
  ["value"]=> string(4) "2 GB" 
  ["new"]=> string(1) "0" 
  ["delete"]=> string(1) "0" } 
 } 
["flag"]=> string(1) "1" } 

/*
 * source
array(12) {

  ["images"]=>
  array(1) {
    [0]=>
    array(2) {
      ["id"]=>
      string(1) "2"
      ["name"]=>
      string(15) "cool_photo.jpeg"
    }
  }
  ["categories"]=>
  array(1) {
    [0]=>
    array(1) {
      ["id"]=>
      string(1) "1"
    }
  }
  ["all_categories"]=>
  array(1) {
    [0]=>
    array(2) {
      ["id"]=>
      string(1) "1"
      ["name"]=>
      string(9) "Notebooks"
    }
  }

  ["params"]=>
  array(2) {
    [0]=>
    array(3) {
      ["id"]=>
      string(1) "1"
      ["name"]=>
      string(10) "Экран"
      ["value"]=>
      string(36) "15.6" WXGA (1366x768) LED Anti-glare"
    }
    [1]=>
    array(3) {
      ["id"]=>
      string(1) "2"
      ["name"]=>
      string(18) "Процессор"
      ["value"]=>
      string(41) "Intel® Pentium® Dual-Core B960 (2.2GHz)"
    }
  }
  ["all_params"]=>
  array(3) {
    [0]=>
    array(2) {
      ["id"]=>
      string(1) "1"
      ["name"]=>
      string(10) "Экран"
    }
    [1]=>
    array(2) {
      ["id"]=>
      string(1) "2"
      ["name"]=>
      string(18) "Процессор"
    }
    [2]=>
    array(2) {
      ["id"]=>
      string(1) "3"
      ["name"]=>
      string(35) "Оперативная память"
    }
  }
}*/




/*
 * form
array(11) {
  
  ["id"]=>
  string(1) "1"
  
  
  ["params"]=>
  array(2) {
    [0]=>
    array(4) {
      ["params"]=>
      string(1) "1"
      ["value"]=>
      string(36) "15.6" WXGA (1366x768) LED Anti-glare"
      ["new"]=>
      string(1) "0"
      ["delete"]=>
      string(1) "0"
    }
    [1]=>
    array(4) {
      ["params"]=>
      string(1) "2"
      ["value"]=>
      string(41) "Intel® Pentium® Dual-Core B960 (2.2GHz)"
      ["new"]=>
      string(1) "0"
      ["delete"]=>
      string(1) "0"
    }
  }
  
  ["images"]=>
  array(1) {
    [0]=>
    array(3) {
      ["path"]=>
      string(0) ""
      ["new"]=>
      string(1) "0"
      ["delete"]=>
      string(1) "0"
    }
  }
  
  ["flag"]=>
  string(1) "1"
}
*/


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

	}
