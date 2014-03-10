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


class Display_Model extends Pattern_Model
	{
/*-------------------------------------------------------------------------
* Constructor of User_Model
--------------------------------------------------------------------------*/
	function __construct()
		{
		parent::__construct();
		$this->view=new View;
		$this->form=parent::Check();
		}



	function Product($param)
		{
		try
			{
			global $config,$sql;
			$product_id=$param[0];
			$data=$sql->Select(array("tablename"=>'products',
									"fields"=>array('id','manufacturer_id','product_name','description','price','amount'),
									"where"=>array(array("field"=>'id',
														"symbol"=>'=',
														"value"=>$product_id),
													array("tablename"=>'products',
														"field"=>'is_deleted',
														"symbol"=>'=',
														"value"=>0),
													array("tablename"=>'products',
														"field"=>'is_visible',
														"symbol"=>'=',
														"value"=>1)),
									"single"=>'single'));

			if($data)
				{
				$data["images"]=$sql->Select(array("tablename"=>'products_images',
													"fields"=>array("tablename"=>'images',
																	"fields"=>array('path')),
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
																		"value"=>0),
																	array("tablename"=>'images',
																		"field"=>'is_visible',
																		"symbol"=>'=',
																		"value"=>1)),
													"order_by"=>array("tablename"=>'products_images',
																		"field"=>'sort')));
				if(!$data["images"])
					{
					$data["images"]='';
					}
				$data["params"]=$sql->Select(array("tablename"=>'params',
													"fields"=>array(array("tablename"=>'params',
																		"fields"=>array('param_name')),
																	array("tablename"=>'products_params',
																		"fields"=>array('value'))),
													"join"=>array("join"=>'left',
																	"existed_table"=>'params',
																	"existed_field"=>'id',
																	"added_table"=>'products_params',
																	"added_field"=>'param_id'),
													"where"=>array(array("tablename"=>'products_params',
																		"field"=>'product_id',
																		"symbol"=>'=',
																		"value"=>$product_id),
																	array("tablename"=>'products_params',
																		"field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
																	array("tablename"=>'products_params',
																		"field"=>'is_visible',
																		"symbol"=>'=',
																		"value"=>1),
																	array("tablename"=>'params',
																		"field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
																	array("tablename"=>'params',
																		"field"=>'is_visible',
																		"symbol"=>'=',
																		"value"=>1)),
													"order_by"=>array("tablename"=>'products_params',
																	"field"=>'sort')));
				$manufacturer=$sql->Select(array("tablename"=>'manufacturers',
												"fields"=>array('manufacturer_name'),
												"where"=>array(array("field"=>'id',
																	"symbol"=>'=',
																	"value"=>$data["manufacturer_id"]),
																array("field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0),
																array("field"=>'is_visible',
																	"symbol"=>'=',
																	"value"=>1)),
												"single"=>'single'));
				$data["manufacturer"]=$manufacturer["manufacturer_name"];
				}
			else
				{
				$data=array("id"=>$product_id,"manufacturer_id"=>'',"product_name"=>'',"description"=>'',"price"=>'',"amount"=>'',"manufacturer"=>'',"images"=>array(),"params"=>array());
				}
			$data["display_advertisment"]=$config["advertisment"];

			$content["content"]=$this->view->Content_Create(__METHOD__,$data);
			$content["assets"]=implode("\n",$this->assets)."\n";
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}



	function Products()
		{
		try
			{
			global $config,$sql;
			$sub_select=$sql->Select(array("tablename"=>'products_images',
											"fields"=>array(array("tablename"=>'products_images',
																	"fields"=>array('product_id','image_id')),
															array("tablename"=>'images',
																	"fields"=>array('path'))),
											"join"=>array("join"=>'left',
															"existed_table"=>'products_images',
															"existed_field"=>'image_id',
															"added_table"=>'images',
															"added_field"=>'id'),
											"where"=>array(array("tablename"=>'images',
																"field"=>'is_deleted',
																"symbol"=>'=',
																"value"=>0),
															array("tablename"=>'images',
																"field"=>'is_visible',
																"symbol"=>'=',
																"value"=>1)),
											"order_by"=>array("tablename"=>'products_images',
															"field"=>'sort'),
											"query"=>'query'));
			$data["products"]=$sql->Select(array("tablename"=>'categories_products',
												"fields"=>array(array("tablename"=>'products',
																	"fields"=>array('id','short_description','product_name')),
																array("tablename"=>'product_images',
																	"fields"=>array('path'))),
												"join"=>array(array("join"=>'left',
																	"existed_table"=>'categories_products',
																	"existed_field"=>'product_id',
																	"added_table"=>'products',
																	"added_field"=>'id'),
																array("existed_table"=>'categories_products',
																	"existed_field"=>'category_id',
																	"added_table"=>'categories',
																	"added_field"=>'id'),
																array("join"=>'left',
																	"sql"=>$sub_select,
																	"existed_table"=>'products',
																	"existed_field"=>'id',
																	"added_table"=>'product_images',
																	"added_field"=>'product_id')),
												"where"=>array(array("tablename"=>'categories_products',
																	"field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0),
																array("tablename"=>'products',
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
																	"value"=>1),
																array("tablename"=>'products',
																	"field"=>'is_visible',
																	"symbol"=>'=',
																	"value"=>1)),
												"group_by"=>array("tablename"=>'products',
																"field"=>'id'),
												"order_by"=>array("tablename"=>'products',
																"field"=>'sort'),
												"having"=>array("sql"=>'min(`products`.`id`)')));
			$data["display_advertisment"]=$config["advertisment"];
			
			$content["content"]=$this->view->Content_Create(__METHOD__,$data);
			$content["assets"]=implode("\n",$this->assets)."\n";
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}



	function Categories()
		{
		try
			{
			global $config,$sql;
			$sub_select=$sql->Select(array("tablename"=>'images',
											"fields"=>array('id','path'),
											"where"=>array(array("field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0),
															array("field"=>'is_visible',
																	"symbol"=>'=',
																	"value"=>1)),
											"single"=>'single',
											"query"=>'query'));
			$data["categories"]=$sql->Select(array("tablename"=>'categories',
												"fields"=>array(array("tablename"=>'categories',
																	"fields"=>array('id','category_name','description')),
																array("tablename"=>'images',
																	"fields"=>array('path'))),
												"join"=>array("join"=>'left',
																"sql"=>$sub_select,
																"existed_table"=>'categories',
																"existed_field"=>'image_id',
																"added_table"=>'images',
																"added_field"=>'id'),
												"where"=>array(array("tablename"=>'categories',
																		"field"=>'is_deleted',
																		"symbol"=>'=',
																		"value"=>0),
																array("tablename"=>'categories',
																		"field"=>'is_visible',
																		"symbol"=>'=',
																		"value"=>1)),
												"order_by"=>array("tablename"=>'categories',
																	"field"=>'sort')));
			$data["display_advertisment"]=$config["advertisment"];

			$content["content"]=$this->view->Content_Create(__METHOD__,$data);
			$content["assets"]=implode("\n",$this->assets)."\n";
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}



	function Category($param)
		{
		try
			{
			global $config,$sql;
			$category_id=$param[0];
			$sub_select=$sql->Select(array("tablename"=>'products_images',
											"fields"=>array(array("tablename"=>'products_images',
																	"fields"=>array('product_id','image_id')),
															array("tablename"=>'images',
																	"fields"=>array('path'))),
											"join"=>array("join"=>'left',
															"existed_table"=>'products_images',
															"existed_field"=>'image_id',
															"added_table"=>'images',
															"added_field"=>'id'),
											"where"=>array(array("tablename"=>'images',
																"field"=>'is_deleted',
																"symbol"=>'=',
																"value"=>0),
															array("tablename"=>'images',
																"field"=>'is_visible',
																"symbol"=>'=',
																"value"=>1)),
											"order_by"=>array("tablename"=>'products_images',
															"field"=>'sort'),
											"query"=>'query'));
			$data["products"]=$sql->Select(array("tablename"=>'categories_products',
												"fields"=>array(array("tablename"=>'products',
																	"fields"=>array('id','short_description','product_name')),
																array("tablename"=>'product_images',
																	"fields"=>array('path'))),
												"join"=>array(array("join"=>'left',
																	"existed_table"=>'categories_products',
																	"existed_field"=>'product_id',
																	"added_table"=>'products',
																	"added_field"=>'id'),
																array("existed_table"=>'categories_products',
																	"existed_field"=>'category_id',
																	"added_table"=>'categories',
																	"added_field"=>'id'),
																array("join"=>'left',
																	"sql"=>$sub_select,
																	"existed_table"=>'products',
																	"existed_field"=>'id',
																	"added_table"=>'product_images',
																	"added_field"=>'product_id')),
												"where"=>array(array("tablename"=>'categories_products',
																	"field"=>'category_id',
																	"symbol"=>'=',
																	"value"=>$category_id),
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
																	"value"=>1),
																array("tablename"=>'products',
																	"field"=>'is_deleted',
																	"symbol"=>'=',
																	"value"=>0),
																array("tablename"=>'products',
																	"field"=>'is_visible',
																	"symbol"=>'=',
																	"value"=>1)),
												"group_by"=>array("tablename"=>'products',
																"field"=>'id'),
												"order_by"=>array("tablename"=>'products',
																"field"=>'sort'),
												"having"=>array("sql"=>'min(`products`.`id`)')));
			$data["display_advertisment"]=$config["advertisment"];

			$content["content"]=$this->view->Content_Create(__METHOD__,$data);
			$content["assets"]=implode("\n",$this->assets)."\n";
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}
	}
