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



	function Users()
		{
		try
			{
			global $db,$sql;
			if(count($this->form)>0)
				{
				foreach($this->form as $id=>$value)
					{
					if(is_numeric($id))
						{
						$sql->Update(array("tablename"=>'users',
											"set"=>array("is_admin"=>isset($value["is_admin"])?1:0,
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$id)));
						if($value["delete"])
							{
							$delete_image=$sql->Select(array("tablename"=>'users_images',
															"fields"=>array('image_id'),
															"where"=>array("field"=>'user_id',
																			"symbol"=>'=',
																			"value"=>$id),
															"single"=>'single'));
							if(isset($delete_image))
								{
								$sql->Update(array("tablename"=>'images',
													"set"=>array("is_deleted"=>1),
													"where"=>array('id','=',$delete_image["image_id"])));

								$deleted_file=$sql->Select(array("tablename"=>'images',
																"fields"=>array('path'),
																"where"=>array("field"=>'id',
																				"symbol"=>'=',
																				"value"=>$delete_image["image_id"]),
																"single"=>'single'));
								if(is_array($deleted_file) && isset($deleted_file["path"]))
									{
									$deleted_dir=ROOT_DIR.DS.'image'.DS.'users'.DS.$id;
									if(file_exists($deleted_dir.DS.$deleted_file["path"]))
										{
										unlink($deleted_dir.DS.$deleted_file["path"]); //Delete file
										if(count(scandir($deleted_dir))==2) //If dir is emty delete it too.
											{
											if(!rmdir($deleted_dir))
												{
												throw new Error("Файл удален, но удалить пустую директорию не удалось");
												}
											}
										}
									else
										{
										throw new Error("Удаляемый файл отсутствует на диске");
										}
									}
								}
							}
						}
					}
				}
			$sub_select=$sql->Select(array("tablename"=>'users_images',
										"join"=>array("existed_table"=>'users_images',
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
										"order_by"=>array("tablename"=>'users_images',
														"field"=>'user_id'),
										"query"=>'query'));
			$users_array=$sql->Select(array("tablename"=>'users',
												"fields"=>array(array("tablename"=>'users',
																	"fields"=>array('id','email','first_name','last_name','country_id','region_id','city_id','address','phone','is_visible','is_admin')),
																array("tablename"=>'user_images',
																	"fields"=>array('path'))),
												"join"=>array("join"=>'left',
																"sql"=>$sub_select,
																"existed_table"=>'users',
																"existed_field"=>'id',
																"added_table"=>'user_images',
																"added_field"=>'user_id'),
												"where"=>array("tablename"=>'users',
																"field"=>'is_deleted',
																"symbol"=>'=',
																"value"=>0),
												"group_by"=>array("tablename"=>'users',
																"field"=>'id'),
												"order_by"=>array("tablename"=>'users',
																"field"=>'id'),
												"having"=>array("sql"=>'min(`users`.`id`)')));
			$users_content=$this->view->Content_Create(__METHOD__,$users_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($users_content);
		}



	function User($args)
		{
		try
			{
			global $db,$sql;
			list($user_id)=$args;
			if(count($this->form)>0)
				{
				$user_id=$this->helper->Admin_User_Save($this->form);
				}
			if($user_id>0)
				{
				$user_array=$sql->Select(array("tablename"=>'users',
												"fields"=>array('id','email','first_name','last_name','country_id','region_id','city_id','address','phone','is_visible','is_admin'),
												"where"=>array(array("field"=>'id',
																	"symbol"=>'=',
																	"value"=>$user_id),
																array("field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0)),
												"single"=>'single'));

				$user_array["images"]=$sql->Select(array("tablename"=>'users_images',
															"fields"=>array("tablename"=>'images',
																			"fields"=>array('id','image_name','path','is_visible')),
															"join"=>array("join"=>'left',
																		"existed_table"=>'users_images',
																		"existed_field"=>'image_id',
																		"added_table"=>'images',
																		"added_field"=>'id'),
															"where"=>array(array("tablename"=>'users_images',
																				"field"=>'user_id',
																				"symbol"=>'=',
																				"value"=>$user_id),
																			array("tablename"=>'images',
																				"field"=>'is_deleted',
																				"symbol"=>'=',
																				"value"=>0))));
				}
			else
				{
				$user_array["id"]=$user_id;
				$user_array["is_visible"]=1;
				$user_array["email"]=
				$user_array["first_name"]=
				$user_array["last_name"]=
				$user_array["country_id"]=
				$user_array["region_id"]=
				$user_array["city_id"]=
				$user_array["address"]=
				$user_array["phone"]=
				$user_array["images"]='';
				}
			$user_array["countries"]=$sql->Select(array("tablename"=>'countries',
														"fields"=>array('id','country_name'),
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$user_array["regions"]=$sql->Select(array("tablename"=>'regions',
														"fields"=>array('id','region_name'),
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$user_array["cities"]=$sql->Select(array("tablename"=>'cities',
														"fields"=>array('id','city_name'),
														"where"=>array("field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
														"order_by"=>array("field"=>'sort')));
			$user_content=$this->view->Content_Create(__METHOD__,$user_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($user_content);
		}



	function Pages()
		{
		try
			{
			global $sql;
			if(count($this->form)>0)
				{
				foreach($this->form as $id=>$value)
					{
					if(is_numeric($id))
						{
						$sql->Update(array("tablename"=>'pages',
											"set"=>array("alias"=>$value["alias"],
														"page_name"=>$value["page_name"],
														"is_visible"=>$value["is_visible"],
														"is_deleted"=>$value["delete"]),
											"where"=>array('id','=',$id)));
						if($value["delete"])
							{
							$delete_image=$sql->Select(array("tablename"=>'pages_images',
															"fields"=>array('image_id'),
															"where"=>array("field"=>'page_id',
																			"symbol"=>'=',
																			"value"=>$id),
															"single"=>'single'));
							if(isset($delete_image))
								{
								$sql->Update(array("tablename"=>'images',
													"set"=>array("is_deleted"=>1),
													"where"=>array('id','=',$delete_image["image_id"])));

								$deleted_file=$sql->Select(array("tablename"=>'images',
																"fields"=>array('path'),
																"where"=>array("field"=>'id',
																				"symbol"=>'=',
																				"value"=>$delete_image["image_id"]),
																"single"=>'single'));
								if(is_array($deleted_file) && isset($deleted_file["path"]))
									{
									$deleted_dir=ROOT_DIR.DS.'image'.DS.'pages'.DS.$id;
									if(file_exists($deleted_dir.DS.$deleted_file["path"]))
										{
										unlink($deleted_dir.DS.$deleted_file["path"]); //Delete file
										if(count(scandir($deleted_dir))==2) //If dir is emty delete it too.
											{
											if(!rmdir($deleted_dir))
												{
												throw new Error("Файл удален, но удалить пустую директорию не удалось");
												}
											}
										}
									else
										{
										throw new Error("Удаляемый файл отсутствует на диске");
										}
									}
								}
							}
						}
					}
				}
			$users_array=$sql->Select(array("tablename"=>'pages',
											"fields"=>array('id','alias','page_name','is_visible'),
											"where"=>array("field"=>'is_deleted',
															"symbol"=>'=',
															"value"=>0),
											"order_by"=>array("field"=>'id')));
			$users_content=$this->view->Content_Create(__METHOD__,$users_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($users_content);
		}



	function Page($args)
		{
		try
			{
			global $sql;
			list($page_id)=$args;
			if(count($this->form)>0)
				{
				$page_id=$this->helper->Admin_Page_Save($this->form);
				}
			if($page_id>0)
				{
				$page_array=$sql->Select(array("tablename"=>'pages',
												"fields"=>array('alias','content','page_name','is_visible'),
												"where"=>array(array("field"=>'id',
																	"symbol"=>'=',
																	"value"=>$page_id),
																array("field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0)),
												"single"=>'single'));

				$page_array["images"]=$sql->Select(array("tablename"=>'pages_images',
															"fields"=>array("tablename"=>'images',
																			"fields"=>array('id','image_name','path')),
															"join"=>array("join"=>'left',
																		"existed_table"=>'pages_images',
																		"existed_field"=>'image_id',
																		"added_table"=>'images',
																		"added_field"=>'id'),
															"where"=>array(array("tablename"=>'pages_images',
																				"field"=>'page_id',
																				"symbol"=>'=',
																				"value"=>$page_id),
																			array("tablename"=>'images',
																				"field"=>'is_deleted',
																				"symbol"=>'=',
																				"value"=>0))));
				}
			else
				{
				$page_array["is_visible"]=1;
				$page_array["alias"]=
				$page_array["content"]=
				$page_array["page_name"]='';
				$page_array["images"]=array();
				}
			$page_array["id"]=$page_id;
			$page_content=$this->view->Content_Create(__METHOD__,$page_array);
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $this->Main($page_content);
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

									$deleted_file=$sql->Select(array("tablename"=>'images',
																	"fields"=>array('path'),
																	"where"=>array("field"=>'id',
																					"symbol"=>'=',
																					"value"=>$delete_image_item["image_id"]),
																	"single"=>'single'));
									if(is_array($deleted_file) && isset($deleted_file["path"]))
										{
										$deleted_dir=ROOT_DIR.DS.'image'.DS.'products'.DS.$value["id"];
										if(file_exists($deleted_dir.DS.$deleted_file["path"]))
											{
											unlink($deleted_dir.DS.$deleted_file["path"]); //Delete file
											if(count(scandir($deleted_dir))==2) //If dir is emty delete it too.
												{
												if(!rmdir($deleted_dir))
													{
													throw new Error("Файл удален, но удалить пустую директорию не удалось");
													}
												}
											}
										else
											{
											throw new Error("Удаляемый файл отсутствует на диске");
											}
										}
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
