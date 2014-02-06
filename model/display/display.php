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
		require_once('helper.php');
		$this->helper=new Display_Helper;
		}



	function Product($param)
		{
		try
			{
			global $db;
			$product_id=$param[0];
			$content['assets']=implode("\n",$this->assets)."\n";
			//$db=Db::Get_Instance();
			$sql="SELECT `id`,`manufacturer_id`,`product_name`,`description`,`price`,`amount` FROM `products` WHERE `id`=:id AND `is_visible`='1';";
			$request=$db->prepare($sql);
			$request->execute(array(':id'=>$product_id));
			$product=$request->fetchAll(PDO::FETCH_ASSOC);
			if($product)
				{
				$product=$product[0];
				$sql="SELECT `i`.`path` FROM `products_images` as `pi` LEFT JOIN `images` as `i` ON `pi`.`image_id`=`i`.`id` WHERE `pi`.`product_id`=:id AND `i`.`is_visible`='1' ORDER BY `pi`.`sort`;";
				$request=$db->prepare($sql);
				$request->execute(array(':id'=>$product_id));
				$images=$request->fetchAll(PDO::FETCH_ASSOC);
				$sql="SELECT `p`.`param_name`,`pp`.`value` FROM `params` as `p` LEFT JOIN `products_params` as `pp` ON `p`.`id`=`pp`.`param_id` WHERE `pp`.`product_id`=:id AND `p`.`is_visible`='1' ORDER BY `p`.`id`;";
				$request=$db->prepare($sql);
				$request->execute(array(':id'=>$product_id));
				$params=$request->fetchAll(PDO::FETCH_ASSOC);
				$sql="SELECT `manufacturer_name` FROM `manufacturers` WHERE `id`=:id;";
				$request=$db->prepare($sql);
				$request->execute(array(':id'=>$product['manufacturer_id']));
				$product['manufacturer']=$request->fetchAll(PDO::FETCH_ASSOC)[0]['manufacturer_name'];
				if(isset($images) && is_array($images))
					{
					$product['images']='';
					//foreach($images as $image)
						{
						$product['images'].='<img src="'.DS.'image'.DS.'products'.DS.$product_id.DS.$images[0]['path'].'" width="400">';
						}
					}
				else
					{
					$product['images'].='<img src="'.DS.'image'.DS.'no_image.png" width="400">';
					}
				$product['params']='<table border="0" cellpadding="10">';
				foreach($params as $param)
					{
					$product['params'].='<tr><td><b>'.$param['param_name'].'</b><td>'.$param['value'].'</tr>';
					}
				$product['params'].='</table>';
				$config=Config::Get_Instance()->Get_Config();
				$product['advertisment']=$config['advertisment'];
				$content['content']=$this->view->Content_Create(__METHOD__,$product);
				}
			else
				{
				throw new Error('Undefined product');
				}
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
			$content['assets']=implode("\n",$this->assets)."\n";
			$content['content']=$this->view->Content_Create(__METHOD__,$this->helper->Display_Products());
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
			$content['assets']=implode("\n",$this->assets)."\n";
			$content['content']=$this->view->Content_Create(__METHOD__,$this->helper->Display_Categories());
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
			$category_id=$param[0];
			$content['assets']=implode("\n",$this->assets)."\n";
			$content['content']=$this->view->Content_Create(__METHOD__,$this->helper->Display_Products($category_id));
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $content;
		}
	}
