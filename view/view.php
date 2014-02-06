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
		global $session,$db;
		$sql="SELECT `sort`,`link`,`title` FROM `menus` WHERE `is_visible`='1' ORDER BY `sort`;";
		$request=$db->prepare($sql);
		$request->execute();
		$menus=$request->fetchAll();
		$content='';
		foreach($menus as $menu)
			{
			if($menu['title']=='Admin')
				{
				if($session->Check('user','is_visible') && $session->Check('user','is_activated') && $session->Check('user','is_admin'))
					{
					if($session->Get('user','is_visible') && $session->Get('user','is_activated') && $session->Get('user','is_admin'))
						{
						$content.=Html::Tag('li',array(),Html::Tag('a',array('href'=>SUB_DIR.$menu['link'],'class'=>'link'),$menu['title']));
						}
					}
				}
			else
				{
				$content.=Html::Tag('li',array(),Html::Tag('a',array('href'=>SUB_DIR.$menu['link'],'class'=>'link'),$menu['title']));
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
			throw new Error('Wrong URL. Error calling unknow method '.$name);
			}
		catch (Error $e)
			{
			$e->Error();
			}	
		}
	}
