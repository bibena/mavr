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
 * Redirect to some page
 *
 * Redirect::Page();
--------------------------------------------------------------------------*/


class Html
	{
	private function __construct()
		{}
	private function __clone()
		{}
	private function __wakeup()
		{}



/*-------------------------------------------------------------------------
 * Redirect to some page
 *
 * Redirect::Link()
 *
 * Return: nothing
--------------------------------------------------------------------------*/
	public static function Tag($tag,array $attrs=array(),$content='')
		{
		try
			{
			$tag=strtolower($tag);
			if(preg_match('|^[a-z]*$|',$tag))
				{
				$result='<'.$tag;
				foreach($attrs as $attr=>$value)
					{
					$value=trim($value);
					$attr=strtolower($attr);
					if(in_array($attr,array('class','id','name','required','value','type','tabindex','disabled','multiple
','size','checked','for')))
						{
						if(preg_match('|^[A-Za-z0-9- _/*+.,]*$|',$value))
							{
							$result.=' '.$attr.'="'.$value.'"';
							}
						else
							{
							throw new Error('Wrong attribute value');
							}
						}
					else
						{
						throw new Error('Wrong attribute name');
						}
					}
				$result.='>';
				$result.=$content;
				if(in_array($tag,array('select','option','div','label')))
					{
					$result.='</'.$tag.'>';
					}
				}
			else
				{
				throw new Error('Wrong tag name');
				}
			}
		catch (Error $e)
			{
			$e->Error();
			}
		return $result;
		}
	}
