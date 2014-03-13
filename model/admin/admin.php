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



	function Main($include='')
		{
		try
			{
			$content['assets']=implode("\n",$this->assets)."\n";
			$content['content']=$this->view->Content_Create(__METHOD__,$include);
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
				$error=$this->helper->Admin_Config_Save($this->form);
				if($error)
					{
					$config_content=$this->view->Content_Create(__METHOD__,$this->helper->Admin_Config_Display($this->form));
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
					$config_content=$this->view->Content_Create(__METHOD__,$this->helper->Admin_Config_Display());
					}
				}
			else
				{
				$config_content=$this->view->Content_Create(__METHOD__,$this->helper->Admin_Config_Display());				
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($config_content);
		}


	function Menu()
		{
		try
			{
			global $db,$sql;
			if(count($this->form)>0)
				{
				foreach($this->form as $sort=>$value)
					{
					if(is_numeric($sort))
						{
						if($value["id"]>0 && $value["id"]!=-1)
							{
							$sql->Update(array("tablename"=>'menus',
												"set"=>array("sort"=>$sort,
															"link"=>trim(SUB_DIR.$value["path"],'/'),
															"title"=>$value["title"],
															"login_only"=>isset($value["login_only"])?1:0,
															"admin_only"=>(isset($value["login_only"]) && isset($value["admin_only"]))?1:0,
															"is_deleted"=>$value["delete"],
															"is_visible"=>$value["is_visible"]),
												"where"=>array('id','=',$value["id"])));
							}
						else
							{
							$sql->Insert(array("tablename"=>'menus',
												"set"=>array("sort"=>$sort,
															"link"=>trim(SUB_DIR.$value["path"],'/'),
															"title"=>$value["title"],
															"login_only"=>isset($value["login_only"])?1:0,
															"admin_only"=>(isset($value["login_only"]) && isset($value["admin_only"]))?1:0,
															"is_visible"=>$value["is_visible"])));
							}
						}
					}
				}
			$menus_array=$sql->Select(array("tablename"=>'menus',
											"where"=>array("field"=>'is_deleted',
															"symbol"=>'=',
															"value"=>0),
											"order_by"=>array("field"=>'sort')));
			$menu_content=$this->view->Content_Create(__METHOD__,$menus_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($menu_content);
		}



	function Acl()
		{
		try
			{
			global $db,$sql;
			if(count($this->form)>0)
				{
				foreach($this->form as $sort=>$value)
					{
					if(is_numeric($sort))
						{
						if($value["id"]>0 && $value["id"]!=-1)
							{
							$sql->Update(array("tablename"=>'acl',
												"set"=>array("login_only"=>isset($value["login_only"])?1:0,
															"admin_only"=>(isset($value["login_only"]) && isset($value["admin_only"]))?1:0),
												"where"=>array('id','=',$value["id"])));
							}
						else
							{
							if((isset($value["login_only"]) && isset($value["admin_only"])) || isset($value["login_only"]))
								{
								$sql->Insert(array("tablename"=>'acl',
													"set"=>array("class_name"=>$value["class_name"],
																"method_name"=>$value["method_name"],
																"login_only"=>1,
																"admin_only"=>(isset($value["admin_only"]))?1:0)));
								}
							}
						}
					}
				}
			$acl_array=Acl::Get_Current_Methods();
			$acl_content=$this->view->Content_Create(__METHOD__,$acl_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($acl_content);
		}



	function Shop()
		{
		try
			{
			global $db,$sql;
			$shop_array=array();

			if(count($this->form)>0)
				{
				switch($this->form["tab"])
					{
					case 'categories':
						$this->helper->Admin_Shop_Categories_Save($this->form);
						break;
					case 'manufacturers':
						$this->helper->Admin_Shop_Manufacturers_Save($this->form);
						break;
					case 'params':
						$this->helper->Admin_Shop_Params_Save($this->form);
						break;
					case 'countries':
						$this->helper->Admin_Shop_Countries_Save($this->form);
						break;
					case 'regions':
						$this->helper->Admin_Shop_Regions_Save($this->form);
						break;
					case 'cities':
						$this->helper->Admin_Shop_Cities_Save($this->form);
						break;
					default:
						break;
					}
				}
			$shop_array["categories"]=$sql->Select(array("tablename"=>'categories',
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$shop_array["manufacturers"]=$sql->Select(array("tablename"=>'manufacturers',
															"where"=>array("field"=>'is_deleted',
																			"symbol"=>'=',
																			"value"=>0),
															"order_by"=>array("field"=>'sort')));
			$shop_array["params"]=$sql->Select(array("tablename"=>'params',
													"where"=>array("field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0),
													"order_by"=>array("field"=>'param_name')));
			$shop_array["countries"]=$sql->Select(array("tablename"=>'countries',
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$shop_array["regions"]=$sql->Select(array("tablename"=>'regions',
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$shop_array["cities"]=$sql->Select(array("tablename"=>'cities',
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$shop_content=$this->view->Content_Create(__METHOD__,$shop_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($shop_content);
		}



	function Products()
		{
		try
			{
			global $db,$sql;
			if(count($this->form)>0)
				{
				foreach($this->form as $sort=>$value)
					{
					if(is_numeric($sort))
						{
						$sql->Update(array("tablename"=>'products',
											"set"=>array("sort"=>$sort,
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$value["id"])));
						if($value["delete"])
							{
							$sql->Update(array("tablename"=>'categories_products',
												"set"=>array("is_deleted"=>1),
												"where"=>array('product_id','=',$value["id"])));
							$delete_image=$sql->Select(array("tablename"=>'products_images',
															"fields"=>array('image_id'),
															"where"=>array("field"=>'product_id',
																			"symbol"=>'=',
																			"value"=>$value["id"])));
							if(isset($delete_image[0]))
								{
								foreach($delete_image as $delete_image_item)
									{
									$sql->Update(array("tablename"=>'images',
														"set"=>array("is_deleted"=>1),
														"where"=>array('id','=',$delete_image_item["image_id"])));
									}
								}
							$sql->Update(array("tablename"=>'products_params',
												"set"=>array("is_deleted"=>1),
												"where"=>array('product_id','=',$value["id"])));
							}
						}
					}
				}
			$sub_select=$sql->Select(array("tablename"=>'products_images',
										"join"=>array("existed_table"=>'products_images',
													"existed_field"=>'image_id',
													"added_table"=>'images',
													"added_field"=>'id'),
										"where"=>array(array("tablename"=>'images',
															"field"=>'is_visible',
															"symbol"=>'=',
															"value"=>1),
														array("tablename"=>'images',
															"field"=>'is_deleted',
															"symbol"=>'=',
															"value"=>0)),
										"order_by"=>array("tablename"=>'products_images',
														"field"=>'sort'),
										"query"=>'query'));
			$products_array=$sql->Select(array("tablename"=>'products',
												"fields"=>array(array("tablename"=>'products',
																	"fields"=>array('id','short_description','product_name','sort','is_visible')),
																array("tablename"=>'product_images',
																	"fields"=>array('path'))),
												"join"=>array("join"=>'left',
																"sql"=>$sub_select,
																"existed_table"=>'products',
																"existed_field"=>'id',
																"added_table"=>'product_images',
																"added_field"=>'product_id'),
												"where"=>array("tablename"=>'products',
																"field"=>'is_deleted',
																"symbol"=>'=',
																"value"=>0),
												"group_by"=>array("tablename"=>'products',
																"field"=>'id'),
												"order_by"=>array("tablename"=>'products',
																"field"=>'sort'),
												"having"=>array("sql"=>'min(`products`.`id`)')));
			$products_content=$this->view->Content_Create(__METHOD__,$products_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($products_content);
		}



	function Product($args)
		{
		try
			{
			global $db,$sql;
			list($product_id)=$args;
			if(count($this->form)>0)
				{
				$product_id=$this->helper->Admin_Product_Save($this->form);
				}
			if($product_id>0)
				{
				$product_array=$sql->Select(array("tablename"=>'products',
													"fields"=>array('id','manufacturer_id','product_name','description','short_description','price','amount','is_visible'),
													"where"=>array(array("field"=>'id',
																		"symbol"=>'=',
																		"value"=>$product_id),
																	array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0)),
													"single"=>'single'));

				$product_array["images"]=$sql->Select(array("tablename"=>'products_images',
															"fields"=>array(array("tablename"=>'images',
																				"fields"=>array('id','image_name','path','is_visible')),
																			array("tablename"=>'products_images',
																				"fields"=>array('sort'))),
															"join"=>array("join"=>'left',
																		"existed_table"=>'products_images',
																		"existed_field"=>'image_id',
																		"added_table"=>'images',
																		"added_field"=>'id'),
															"where"=>array(array("tablename"=>'products_images',
																				"field"=>'product_id',
																				"symbol"=>'=',
																				"value"=>$product_id),
																			array("tablename"=>'images',
																				"field"=>'is_deleted',
																				"symbol"=>'=',
																				"value"=>0)),
															"order_by"=>array("tablename"=>'products_images',
																				"field"=>'sort')));

				$product_array["categories"]=$sql->Select(array("tablename"=>'categories_products',
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
																				"value"=>$product_id),
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

				$product_array["params"]=$sql->Select(array("tablename"=>'products_params',
															"fields"=>array(array("tablename"=>'params',
																				"fields"=>array('id','param_name')),
																		array("tablename"=>'products_params',
																				"fields"=>array('pp_id','value','sort','is_visible'))),
															"join"=>array("join"=>'left',
																		"existed_table"=>'products_params',
																		"existed_field"=>'param_id',
																		"added_table"=>'params',
																		"added_field"=>'id'),
															"where"=>array(array("tablename"=>'products_params',
																				"field"=>'product_id',
																				"symbol"=>'=',
																				"value"=>$product_id),
																			array("tablename"=>'params',
																				"field"=>'is_visible',
																				"symbol"=>'=',
																				"value"=>1),
																			array("tablename"=>'params',
																				"field"=>'is_deleted',
																				"symbol"=>'=',
																				"value"=>0),
																			array("tablename"=>'products_params',
																				"field"=>'is_deleted',
																				"symbol"=>'=',
																				"value"=>0)),
															"order_by"=>array("tablename"=>'products_params',
																				"field"=>'sort')));
				}
			else
				{
				$product_array["id"]=$product_id;
				$product_array["is_visible"]=1;
				$product_array["manufacturer_id"]=$product_array["product_name"]=$product_array["short_description"]=$product_array["description"]=$product_array["price"]=$product_array["amount"]=$product_array["images"]='';
				$product_array["categories"]=array();
				}
			$product_array["all_categories"]=$sql->Select(array("tablename"=>'categories',
																"where"=>array(array("field"=>'is_deleted',
																					"symbol"=>'=',
																					"value"=>0),
																				array("field"=>'is_visible',
																					"symbol"=>'=',
																					"value"=>1)),
																"order_by"=>array("field"=>'sort')));

			$product_array["manufacturers"]=$sql->Select(array("tablename"=>'manufacturers',
																"where"=>array(array("field"=>'is_deleted',
																					"symbol"=>'=',
																					"value"=>0),
																				array("field"=>'is_visible',
																					"symbol"=>'=',
																					"value"=>1)),
																"order_by"=>array("field"=>'sort')));

			$product_array["all_params"]=$sql->Select(array("tablename"=>'params',
																"where"=>array(array("field"=>'is_deleted',
																					"symbol"=>'=',
																					"value"=>0),
																				array("field"=>'is_visible',
																					"symbol"=>'=',
																					"value"=>1))));

			$product_content=$this->view->Content_Create(__METHOD__,$product_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($product_content);
		}
	}
