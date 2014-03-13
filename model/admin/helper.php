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



	function Config_Input_Construct($type,$title,$value,$options=false)
		{
		$text='
		<div class="form-group">
			<label for="input'.$title.'" class="control-label col-xs-4 col-sm-4 col-md-4 col-lg-4">'.ucfirst($title).'</label>
			<div class="controls col-xs-4 col-sm-4 col-md-4 col-lg-4">';
		if($type=='radio' && is_array($options))
			{
			foreach($options as $option)
				{
				$text.='
				<label class="radio-inline">
					<input type="radio"'.(($value==$option["value"])?' checked':'').' value="'.$option["value"].'" id="input'.$title.'" class="form-control" required="required" name="'.$title.'">'.$option["sign"].'
				</label>';
				}			
			}
		elseif($type='text')
			{
			$text.='
				<input type="input" value="'.$value.'" id="input'.$title.'" class="form-control"'.(($options)?' required':'').' name="'.$title.'">';
			}
		$text.='
			</div>
			<div>
				<span class="help-block col-xs-4 col-sm-4 col-md-4 col-lg-4"></span>
			</div>
		</div>';
		return $text;
		}



/*-------------------------------------------------------------------------
* Example of Check method
*
* User_Helper::Check(array('pass'=>array('password','string_for_check')));
*
* Return: array with bool result for every element.
--------------------------------------------------------------------------*/
	function Admin_Config_Display($form=false)
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
					$options=array(array("sign"=>YES,"value"=>"1"),array("sign"=>NO,"value"=>"0"));
					$result[$tab][$param]=$this->Config_Input_Construct('radio',$param,$value,$options);
					}
				elseif(in_array($param,$db_type_radio))
					{
					$options=array(array("sign"=>"mysql","value"=>"mysql"),array("sign"=>"sqlite","value"=>"sqlite"));
					$result[$tab][$param]=$this->Config_Input_Construct('radio',$param,$value,$options);
					}
				elseif(in_array($param,$dir_radio))
					{
					if($handle=opendir(ROOT_DIR.DS.$param))
						{
						$options=array();
						while(false !== ($entry = readdir($handle)))
							{
							if ($entry != "." && $entry != "..")
								{
								if(is_dir(ROOT_DIR.DS.$param.DS.$entry))
									{
									$options[]=array("sign"=>$entry,"value"=>$entry);
									}
								}
							}
						closedir($handle);
						}
					$result[$tab][$param]=$this->Config_Input_Construct('radio',$param,$value,$options);
					}
				elseif(in_array($param,$non_required_text))
					{
					$result[$tab][$param]=$this->Config_Input_Construct('text',$param,$value);
					}
				else
					{
					$result[$tab][$param]=$this->Config_Input_Construct('text',$param,$value,1);
					}
				}
			}
		if($form)
			{
			$result['error']=$this->Admin_Config_Save($form);
			}
		return $result;	
		}



	function Admin_Config_Save($form)
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
 * Functions to administrate shop setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////



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
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'categories',
											"set"=>array("sort"=>$sort,
														"category_name"=>$value["category_name"],
														"description"=>$value["description"],
														"is_deleted"=>$value["delete"],
														"is_visible"=>$value["is_visible"]),
											"where"=>array('id','=',$value["id"])));
						$category_id=$value["id"];
						}
					else
						{
						if($value["id"]==-1)
							{
							$sql->Insert(array("tablename"=>'categories',
												"set"=>array("sort"=>$sort,
															"category_name"=>$value["category_name"],
															"description"=>$value["description"],
															"is_visible"=>$value["is_visible"])));
							$category_id=$db->lastInsertId();
							}
						}
					if($form["postfiles"][$sort]["name"]["image"])
						{
						$path=call_user_func('end',explode('/',$form["postfiles"][$sort]["tmp_name"]["image"]));
						$sql->Insert(array("tablename"=>'images',
											"set"=>array("image_name"=>$form["postfiles"][$sort]["name"]["image"],
														"path"=>$path,
														"is_visible"=>1)));
						$new_image_id=$db->lastInsertId();
						$sql->Update(array("tablename"=>'categories',
											"set"=>array("image_id"=>$new_image_id),
											"where"=>array('id','=',$category_id)));
						if(!file_exists(ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id))
							{
							mkdir(ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id);
							}
						rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'categories'.DS.$category_id.DS.$path);
						}					
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
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'manufacturers',
							"set"=>array("sort"=>$sort,
										"manufacturer_name"=>$value["manufacturer_name"],
										"is_deleted"=>$value["delete"],
										"is_visible"=>$value["is_visible"]),
							"where"=>array('id','=',$value["id"])));
						}
					else
						{
						$sql->Insert(array("tablename"=>'manufacturers',
											"set"=>array("sort"=>$sort,
														"manufacturer_name"=>$value["manufacturer_name"],
														"is_visible"=>$value["is_visible"])));
						}
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
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'params',
											"set"=>array("param_name"=>$value["param_name"],
														"is_deleted"=>$value["delete"],
														"is_visible"=>$value["is_visible"]),
											"where"=>array('id','=',$value["id"])));
						}
					else
						{
						$sql->Insert(array("tablename"=>'params',
											"set"=>array("param_name"=>$value["param_name"],
														"is_visible"=>$value["is_visible"])));
						}
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
		return true;
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
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'countries',
											"set"=>array("sort"=>$sort,
														"country_name"=>$value["country_name"],
														"phone_code"=>$value["phone_code"],
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$value["id"])));
						}
					else
						{
						$sql->Insert(array("tablename"=>'countries',
											"set"=>array("sort"=>$sort,
														"country_name"=>$value["country_name"],
														"phone_code"=>$value["phone_code"],
														"is_visible"=>$value["is_visible"])));
						}
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
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Regions_Save($form)
		{
		try 
			{
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'regions',
											"set"=>array("sort"=>$sort,
														"country_id"=>$value["country_id"],
														"region_name"=>$value["region_name"],
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$value["id"])));
						}
					else
						{
						$sql->Insert(array("tablename"=>'regions',
											"set"=>array("sort"=>$sort,
														"country_id"=>$value["country_id"],
														"region_name"=>$value["region_name"],
														"is_visible"=>$value["is_visible"])));
						}
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
		return true;
		}



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Shop_Cities_Save($form)
		{
		try 
			{
			global $db,$sql;
			foreach($form as $sort=>$value)
				{
				if(is_numeric($sort))
					{
					if($value["id"]>0 && $value["id"]!=-1)
						{
						$sql->Update(array("tablename"=>'cities',
											"set"=>array("sort"=>$sort,
														"country_id"=>$value["country_id"],
														"region_id"=>($value["region_id"])?$value["region_id"]:null,
														"city_name"=>$value["city_name"],
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$value["id"])));
						}
					else
						{
						$sql->Insert(array("tablename"=>'cities',
											"set"=>array("sort"=>$sort,
														"country_id"=>$value["country_id"],
														"region_id"=>($value["region_id"])?$value["region_id"]:null,
														"city_name"=>$value["city_name"],
														"is_visible"=>$value["is_visible"])));
						}
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
		return true;
		}



/*****************************************************************************************************************
 * 
 * Functions to administrate products setting
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/*****************************************************************************************************************
 * 
 * @input array with POST data from admin panel
 * @return bool true
 * 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Admin_Product_Save($form)
		{
		try
			{
			global $db,$sql;
			if($form["id"]>0)
				{
//---update name,description,price,amount,visibility,modification_time
				$sql->Update(array("tablename"=>'products',
									"set"=>array("manufacturer_id"=>$form["manufacturer_id"],
												"product_name"=>$form["product_name"],
												"short_description"=>$form["short_description"],
												"description"=>$form["description"],
												"price"=>$form["price"],
												"amount"=>$form["amount"],
												"is_visible"=>isset($form["is_visible"])?1:0),
									"where"=>array('id','=',$form["id"])));
				}
			else
				{
				$sql->Insert(array("tablename"=>'products',
									"set"=>array("manufacturer_id"=>$form["manufacturer_id"],
												"product_name"=>$form["product_name"],
												"short_description"=>$form["short_description"],
												"description"=>$form["description"],
												"price"=>$form["price"],
												"amount"=>$form["amount"],
												"is_visible"=>$form["is_visible"])));
				$form["id"]=$db->lastInsertId();
				}
//---update categories
			$categories_source=$sql->Select(array("tablename"=>'categories_products',
														"fields"=>array(array("tablename"=>'categories',
																			"fields"=>array('id'))),
														"join"=>array("join"=>'left',
																	"existed_table"=>'categories_products',
																	"existed_field"=>'category_id',
																	"added_table"=>'categories',
																	"added_field"=>'id'),
														"where"=>array(array("tablename"=>'categories_products',
																			"field"=>'product_id',
																			"symbol"=>'=',
																			"value"=>$form["id"]),
																		array("tablename"=>'categories_products',
																			"field"=>'is_deleted',
																			"symbol"=>'=',
																			"value"=>0),
																		array("tablename"=>'categories',
																			"field"=>'is_deleted',
																			"symbol"=>'=',
																			"value"=>0),
																		array("tablename"=>'categories',
																			"field"=>'is_visible',
																			"symbol"=>'=',
																			"value"=>1))));

			$categories=array();
			foreach($categories_source as $category_source)
				{
				$categories[]=$category_source["id"];
				}
			$deleted_categories=array_diff($categories,$form["categories"]);
			$added_categories=array_diff($form["categories"],$categories);
			foreach($deleted_categories as $dc)
				{
				$sql->Update(array("tablename"=>'categories_products',
									"set"=>array("is_deleted"=>1),
									"where"=>array(array("product_id",'=',$form["id"]),
													array("category_id",'=',$dc))));
				}
			foreach($added_categories as $ac)
				{
				$sql->Insert(array("tablename"=>'categories_products',
									"set"=>array("category_id"=>$ac,
												"product_id"=>$form["id"])));
				}
//---update params
			if(isset($form["params"]) && is_array($form["params"]))
				{
				foreach($form["params"] as $sort=>$param)
					{
					if($param["pp_id"]=='-1')
						{
						$sql->Insert(array("tablename"=>'products_params',
											"set"=>array("product_id"=>$form["id"],
														"param_id"=>$param["id"],
														"value"=>$param["value"],
														"sort"=>$sort,
														"is_visible"=>$param["is_visible"])));
						}
					else
						{
						$sql->Update(array("tablename"=>'products_params',
											"set"=>array("value"=>$param["value"],
														"sort"=>$sort,
														"is_visible"=>$param["is_visible"],
														"is_deleted"=>$param["delete"]),
											"where"=>array("pp_id",'=',$param["pp_id"])));
						}
					}
				}
//---update images
			if(isset($form["postfiles"]) && isset($form["imagesdata"]))
				{
				foreach($form["imagesdata"] as $sortid=>$imagedata)
					{
					if($imagedata["id"]=="-1")
						{
						if(strpos($form["postfiles"]["images"]["type"][$sortid],'image')!==false)
							{
							$path=call_user_func('end',explode('/',$form["postfiles"]["images"]["tmp_name"][$sortid]));
							$sql->Insert(array("tablename"=>'images',
												"set"=>array("image_name"=>$form["postfiles"]["images"]["name"][$sortid],
															"path"=>$path,
															"is_visible"=>$imagedata["visible"])));
							$lastid=$db->lastInsertId();
							if(!file_exists(ROOT_DIR.DS.'image'.DS.'products'.DS.$form["id"]))
								{
								mkdir(ROOT_DIR.DS.'image'.DS.'products'.DS.$form["id"]);
								}
							rename(ROOT_DIR.DS.'temp'.DS.$path,ROOT_DIR.DS.'image'.DS.'products'.DS.$form["id"].DS.$path);
							$sql->Insert(array("tablename"=>'products_images',
												"set"=>array("image_id"=>$lastid,
															"product_id"=>$form["id"],
															"sort"=>$sortid)));
							}
						}
					else
						{
						$sql->Update(array("tablename"=>'images',
											"set"=>array("is_visible"=>$imagedata["visible"],
														"is_deleted"=>$imagedata["delete"]),
											"where"=>array("id",'=',$imagedata["id"])));
						$sql->Update(array("tablename"=>'products_images',
											"set"=>array("sort"=>$sortid),
											"where"=>array("image_id",'=',$imagedata["id"])));
						}
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
		return $form["id"];
		}
	}
