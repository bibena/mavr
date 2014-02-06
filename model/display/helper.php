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


class Display_Helper
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



	public function Get_Categories()
		{
		try
			{
			$db=Db::Get_Instance();
			$sql="SELECT `c`.`id`,`c`.`category_name`,`c`.`description`,`i`.`path` FROM `categories` AS `c` LEFT JOIN (SELECT `id`,`path` FROM `images` WHERE `is_visible`=1) AS `i` ON `c`.`image_id`=`i`.`id` WHERE `c`.`is_visible` = '1' ORDER BY `c`.`sort`;";
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



	public function Display_Categories()
		{
		try
			{
			$categories_content='';
			foreach($this->Get_Categories() as $category)
				{
				if($category["path"]=='')
					{
					$image_name='no_image.png';
					}
				else
					{
					$image_name='categories'.DS.$category["id"].DS.$category["path"];
					}
				$categories_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_products_item">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a class="link" href="'.SUB_DIR.'display/category'.DS.$category["id"].'"><img style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.$image_name.'"></a>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="line-height:inherit;text-align:left;">
					<div><a class="link" href="'.SUB_DIR.'display/category'.DS.$category["id"].'">'.$category["category_name"].'</a></div>
					<div><span>'.mb_substr($category["description"],0,200,'UTF-8').'&hellip;</span></div>
				</div>
			</div>';
				}
			$content['categories']=$categories_content;
			$config=Config::Get_Instance()->Get_Config();
			$content['advertisment']=$config['advertisment'];
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $content;
		}



	public function Get_Products($category_id=0)
		{
		try
			{
			global $db;
			if(is_numeric($category_id))
				{
				if($category_id>0)
					{
					$request_array=array(':id'=>$category_id);
					$sql="SELECT `p`.`id`,`p`.`description`,`p`.`product_name`,`pi`.`path` FROM `categories_products` AS `cp` LEFT JOIN `products` AS `p` ON `cp`.`product_id`=`p`.`id` LEFT JOIN (SELECT * FROM `products_images` JOIN `images` ON `products_images`.`image_id`=`images`.`id` WHERE `images`.`is_visible`='1' ORDER BY `products_images`.`sort`) as `pi` ON `p`.`id`=`pi`.`product_id` WHERE `p`.`is_visible`='1'  AND `cp`.`category_id`=:id GROUP BY `p`.`id` HAVING min(`p`.`id`);";
					}
				elseif($category_id==0)
					{
					$request_array=array();
					$sql="SELECT `p`.`id`,`p`.`description`,`p`.`product_name`,`pi`.`path` FROM `products` AS `p` LEFT JOIN (SELECT * FROM `products_images` JOIN `images` ON `products_images`.`image_id`=`images`.`id` WHERE `images`.`is_visible`='1' ORDER BY `products_images`.`sort`) as `pi` ON `p`.`id`=`pi`.`product_id` WHERE `p`.`is_visible`='1' GROUP BY `p`.`id` HAVING min(`p`.`id`);";
					}
				else
					{
					throw new Error('Wrong category id');
					}
				$request=$db->prepare($sql);
				$request->execute($request_array);
				$products=$request->fetchAll();
				}
			else
				{
				throw new Error('Category id should be numerical');
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
		return $products;
		}



	public function Display_Products($category_id=0)
		{
		try
			{
			$products_content='';
			foreach($this->Get_Products($category_id) as $product)
				{
				if($product["path"]=='')
					{
					$image_name='no_image.png';
					}
				else
					{
					$image_name='products'.DS.$product["id"].DS.$product["path"];
					}
				$products_content.='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-state-default admin_products_item">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<a class="link" href="'.SUB_DIR.'display/product'.DS.$product["id"].'"><img style="max-width:100%;max-height:140px" src="'.SUB_DIR.'image'.DS.$image_name.'"></a>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="line-height:inherit;text-align:left;">
					<div><a class="link" href="'.SUB_DIR.'display/product'.DS.$product["id"].'">'.$product["product_name"].'</a></div>
					<div><span>'.mb_substr(/*strip_tags(*/$product["description"]/*)*/,0,200,'UTF-8').'&hellip;</span></div>
				</div>
			</div>';
				}
			$content['products']=$products_content;
			$config=Config::Get_Instance()->Get_Config();
			$content['advertisment']=$config['advertisment'];
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $content;
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
			$product['categories']=$request->fetchAll(PDO::FETCH_ASSOC);;
			
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
			$content=array('id'=>$id,
							'options'=>$product_params['options'],
							'general'=>$this->Display_Product_General($product_source,$id),
							'params'=>$product_params['content'],
							'images'=>$this->Display_Product_Images($product_source,$id));
			}
		catch (Error $e) 
			{
			$e->Error();
			}
		return $content;
		}
	}
