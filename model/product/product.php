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


class Product_Model extends Pattern_Model
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
		//$this->helper=new Product_Helper;
		}



	function Show($param)
		{
		try
			{
			$product_id=$param[0];
			$content['assets']=implode("\n",$this->assets)."\n";
			$db=Db::Get_Instance();
			$sql="SELECT `id`,`manufacturer_id`,`product_name`,`description`,`price`,`amount` FROM `products` WHERE `id`=:id AND `is_visible`='1';";
			$request=$db->prepare($sql);
			$request->execute(array(':id'=>$product_id));
			$product=$request->fetchAll(PDO::FETCH_ASSOC);
			if($product)
				{
				$product=$product[0];
				$sql="SELECT `i`.`path` FROM `products_images` as `pi` LEFT JOIN `images` as `i` ON `pi`.`image_id`=`i`.`id` WHERE `pi`.`product_id`=:id AND `pi`.`is_visible`='1' ORDER BY `pi`.`sort`;";
				$request=$db->prepare($sql);
				$request->execute(array(':id'=>$product_id));
				$images=$request->fetchAll(PDO::FETCH_ASSOC);
				if($images)
					{
					$product['image']=DS.'image'.DS.'products'.DS.$product_id.DS.$images[0]['path'];
					}
				else
					{
					$product['image']=DS.'image'.DS.'no_image.png';
					}
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
	}
