<?php
//define all pathes
define('FLAG',1);
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR',dirname(__FILE__));
define('INIT_DIR',ROOT_DIR.DS.'init');
//initialize system
include_once(INIT_DIR.DS.'init.php');
/*$sql=Sql::Get_Instance();
echo'<pre>';
print_r($sql->Select(array("tablename"=>'menus',
											"where"=>array("field"=>'is_deleted',
															"symbol"=>'=',
															"value"=>0),
											"order_by"=>array("field"=>'sort'),
											"query"=>'query')));
echo'</pre>';*/
?>
