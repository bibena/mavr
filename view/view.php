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
* Ancestor of all views
*
* new Pattern_View;
*
--------------------------------------------------------------------------*/


class View
	{
/*-------------------------------------------------------------------------
* Constructor of View
--------------------------------------------------------------------------*/
	function __construct()
		{
//create address of pattern
		$config=Config::Get_Instance()->Get_Config();
		$this->template=ROOT_DIR.DS.'template'.DS.$config['template'].DS.$config['template'].'.html';
		}



/*-------------------------------------------------------------------------
* Example of Content_Create method
*
* View::Content_Create($path,$content);
*
* Create content for displaying on the screen.
--------------------------------------------------------------------------*/
	function Content_Create($method,$content)
		{
		list($folder,$file)=str_replace('_Model','',explode('::',$method));
//create content
		ob_start();
		include_once(strtolower($folder).DS.strtolower($file).".html");
		$return=ob_get_contents();
		ob_end_clean();
		return $return;
		}



/*-------------------------------------------------------------------------
* Example of Display method
*
* Class::Display();
*
* Display content in the screen.
--------------------------------------------------------------------------*/
	function Display($content)
		{
		$content['menu']=$this->Menu();
//output content to the browser
		ob_start();
		include_once($this->template);
		ob_end_flush();
		}



/*-------------------------------------------------------------------------
* Example of Menu method
*
* Class::Menu();
*
* Return menu content.
--------------------------------------------------------------------------*/
	function Menu()
		{
		global $session,$db,$sql;
		$menus=$sql->Select(array("tablename"=>'menus',
									"fields"=>array('sort','link','title','login_only','admin_only'),
									"where"=>array(array("field"=>'is_deleted',
														"symbol"=>'=',
														"value"=>0),
													array("field"=>'is_visible',
														"symbol"=>'=',
														"value"=>1)),
									"order_by"=>'sort'));
		$content='';
		foreach($menus as $menu)
			{
			if($menu['login_only'])
				{
				if($menu['admin_only'])
					{
					if($session->Check('user','is_visible') &&
						$session->Check('user','is_deleted') &&
						$session->Check('user','is_admin') &&
						$session->Get('user','is_visible') &&
						!$session->Get('user','is_deleted') &&
						$session->Get('user','is_admin'))
						{
						$content.='<li><a href="'.SUB_DIR.$menu['link'].'" class="link">'.$menu['title'].'</a></li>';
						}
					}
				else
					{
					if($session->Check('user','is_visible') && 
						$session->Check('user','is_deleted') && 
						$session->Get('user','is_visible') && 
						!$session->Get('user','is_deleted'))
						{
						$content.='<li><a href="'.SUB_DIR.$menu['link'].'" class="link">'.$menu['title'].'</a></li>';
						}
					}
				}
			else
				{
				$content.='<li><a href="'.SUB_DIR.$menu['link'].'" class="link">'.$menu['title'].'</a></li>';
				}
			}
		return $content;
		}



/*-------------------------------------------------------------------------
* Example of __call method
*
* Class::Unknown_Method();
*
* Return: throw an exception.
--------------------------------------------------------------------------*/
	function __call($name, $arguments)
		{
//---if were calling unknown method throw an exception
		try
			{
			throw new Error(ERROR_VIEW_URL.$name);
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	}
