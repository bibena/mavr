<?php

//$start=explode('.',microtime(true));

//header("Content-Type: text/html; charset=UTF-8");
//header("Cache-Control: no-store, no-cache, must-revalidate"); 
//header("Expires: " . date("r"));

define('FLAG',1);
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR',dirname(__FILE__));
define('INIT_DIR',ROOT_DIR.DS.'init');
include_once(INIT_DIR.DS.'init.php');

//var_dump(filter_var('https://example.com?tw', FILTER_VALIDATE_URL));
//var_dump(array('HTTP_HOST'=>$_SERVER['HTTP_HOST'],'REQUEST_URI'=>$_SERVER['REQUEST_URI'],'PATH_INFO'=>$_SERVER['PATH_INFO'],'SCRIPT_NAME'=>$_SERVER['SCRIPT_NAME'],'SERVER_NAME'=>$_SERVER['SERVER_NAME'],'PHP_SELF'=>$_SERVER['PHP_SELF']));
//include('tyrryty.yut');
/*

			$email='23@mail.ru';
			$prepare=Db::Get_Instance()->prepare("SELECT `password` FROM `users` where `email` = :email");
			$prepare->execute(array(':email'=>$email));
			var_dump($prepare->fetchAll(PDO::FETCH_ASSOC));
/*




$res=$db->Query('SELECT * FROM `users`;');

*/
/*
$db=new PDO('sqlite:/media/DISK_A1/web/db/test');

$db->exec('PRAGMA foreign_keys = 1;');

$rr=$db->query('PRAGMA foreign_keys;');
$res=$rr->fetchAll(PDO::FETCH_ASSOC);
var_dump($res);

$stmt = $db->prepare("SELECT * FROM `users` where id = :id");

$stmt->execute(array(':id'=>4));

$res=$stmt->fetchAll(PDO::FETCH_ASSOC);




var_dump($res);

*/


/*
Db::Get_Instance()->Create_Table(array('tablename'=>'users','if_not_exists','column'=>array(
	array('name'=>'id','type'=>'integer','not_null','ai'),
	array('name'=>'email','type'=>'text','not_null'),
	array('name'=>'password','type'=>'text','not_null'),
	array('name'=>'age','type'=>'integer','not_null'),
	array('name'=>'fname','type'=>'text','not_null'),
	array('name'=>'lname','type'=>'text','not_null'),
	array('name'=>'cdate','type'=>'text','not_null')
	),'primary_key'=>array('id')));

Db::Get_Instance()->Create_Table(array('tablename'=>'products','if_not_exists','column'=>array(
	array('name'=>'id','type'=>'integer','not_null','ai'),
	array('name'=>'name','type'=>'text','not_null'),
	array('name'=>'description','type'=>'text','not_null'),
	array('name'=>'price','type'=>'integer','not_null')
	),'primary_key'=>array('id')));

Db::Get_Instance()->Create_Table(array('tablename'=>'orders','if_not_exists','column'=>array(
	array('name'=>'id','type'=>'integer','not_null','ai'),
	array('name'=>'id_product','type'=>'integer','not_null'),
	array('name'=>'id_user','type'=>'integer','not_null')
	),'primary_key'=>array('id'),'foreign_key'=>array(
	array('id_product','products','id','on_update'=>'cascade','on_delete'=>'cascade'),
	array('id_user','users','id','on_update'=>'cascade','on_delete'=>'cascade')
	)));


function ranset($type='i',$min,$max)
	{
	if($type==='i')
		{
		return mt_rand($min,$max);
		}
	else
		{
		$word='';
		for($i=0,$j=mt_rand($min,$max);$i<$j;$i++)
			{
			$word.=chr(mt_rand(97,122));
			}
		return $word;
		}
	}

$lorem='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

for($i=0,$j=50;$i<$j;$i++)
	{
	$email=ranset('s',5,8).'@'.ranset('s',5,8).'.'.ranset('s',2,3);
	Db::Get_Instance()->Insert(array('tablename'=>'users','set'=>array('email'=>$email,'password'=>md5($email),'age'=>ranset('i',10,50),'fname'=>ucfirst(ranset('s',5,8)),'lname'=>ucfirst(ranset('s',5,8)),'cdate'=>time())));
	$id_user[]=Db::Get_Instance()->Insert_Id();
	}

for($i=0,$j=10;$i<$j;$i++)
	{
	Db::Get_Instance()->Insert(array('tablename'=>'products','set'=>array('name'=>ucfirst(ranset('s',5,8)),'description'=>$lorem,'price'=>ranset('i',50,800))));
	$id_product[]=Db::Get_Instance()->Insert_Id();
	}

for($i=0,$j=30;$i<$j;$i++)
	{
	Db::Get_Instance()->Insert(array('tablename'=>'orders','set'=>array('id_product'=>ranset('i',min($id_product),max($id_product)),'id_user'=>ranset('i',min($id_user),max($id_user)))));
	$id_order[]=Db::Get_Instance()->Insert_Id();
	}

var_dump(array('id_user'=>$id_user,'id_product'=>$id_product,'id_order'=>$id_order));
*/

//var_dump(Db::Get_Instance()->Select_Join(array('fields'=>array('products'=>'id'),'tablename'=>'users','where'=>array('users','id','in',array(51,25)),'join'=>array(array('users','id','orders','id_user'),array('orders','id_product','products','id')))));


//var_dump(Db::Get_Instance()->Delete(array('tablename'=>'orders','where'=>array('id','>','1'))));

/*
var_dump(Db::Get_Instance()->Drop_Table(array('tablename'=>'orders')));
var_dump(Db::Get_Instance()->Drop_Table(array('tablename'=>'users')));
var_dump(Db::Get_Instance()->Drop_Table(array('tablename'=>'products')));
*/
/*
$stop=explode('.',microtime(true));

if($stop[0]>=$start[0])
	{
	$exec[]=$stop[0]-$start[0];
	}
else
	{
	$exec[]=$start[0]-$stop[0];
	}
if($stop[1]>=$start[1])
	{
	$exec[]=$stop[1]-$start[1];
	}
else
	{
	$exec[]=$start[1]-$stop[1];
	}

echo "<br>Затрачено: $exec[0].$exec[1]";



//echo Db::Get_Instance()->Update(array('query','tablename'=>'users','limit'=>array('5','5'),'where'=>array('age','=','21'),'set'=>array('name'=>'Иннокентий')));
//var_dump(Db::Get_Instance()->Count(array('tablename'=>'users')));
//var_dump(Db::Get_Instance()->Delete(array('tablename'=>'users','where'=>array('id','>','30'))));
//var_dump(Db::Get_Instance()->Count_Last());
//echo Db::Get_Instance()->Multi_Insert(array('tablename'=>'users',array('set'=>array('name'=>'Валера','age'=>date('s'))),array('set'=>array('name'=>'Вася','age'=>date('s')))));
//echo Db::Get_Instance()->Multi_Insert(array('query','tablename'=>'users',array('set'=>array('name'=>'Валера','age'=>date('s'))),array('set'=>array('name'=>'Вася','age'=>date('s')))));
/*var_dump(array(Db::Get_Instance()->Query("BEGIN TRANSACTION;"),
Db::Get_Instance()->Query("INSERT INTO `users` (`name`,`age`) VALUES ('Валера','21');"),
Db::Get_Instance()->Query("INSERT INTO `users` (`name`,`age`) VALUES ('Вася','21');"),
Db::Get_Instance()->Query("COMMIT;")));
*/









//echo db::Get_Instance()->Create_Table(array('tablename'=>'users','column'=>array(array('name'=>'id','type'=>'integer','ai'),array('name'=>'name','type'=>'text'),array('name'=>'age','type'=>'integer')),'primary_key'=>array('id')));

//echo db::Get_Instance()->Create_Table(array('tablename'=>'offers','column'=>array(array('name'=>'id','type'=>'integer','ai'),array('name'=>'id_users','type'=>'integer'),array('name'=>'text','type'=>'text','default'=>'Describing')),'primary_key'=>array('id'),'foreign_key'=>array('id_users','users','id','on_update'=>'cascade','on_delete'=>'cascade')));

//var_dump(db::Get_Instance()->Query("DELETE FROM `users` WHERE `id`='5';"));

//print_r(db::get_instance()->select_join('fuck',array('fields'=>array('suck'=>'b'),'join'=>array('inner','fuck','a','suck','a'))));

//print_r(db::get_instance()->select('fuck',array('distinct','group_by'=>array('a','DESC'),'where'=>array('a','=','c'),'fields'=>array('a'),'limit'=>1)));
//print_r(db::get_instance()->delete('fuck',array('where'=>array('id','>','13'))));
//echo'<br>'.$db->last_count();
//db::get_instance()->multi_insert('fuck',array(array('ignore','low_priority','data'=>array('a'=>"a'b,b",'b'=>'b')),array('ignore','data'=>array('a'=>'c','b'=>'d'))));
/*for($a=121;$a<=122;$a++)
	{
	if($a<110)
		db::get_instance()->insert('fuck',array('ignore','data'=>array('a'=>chr($a),'b'=>$a)));
	if($a>105)*/
		//print_r(db::get_instance()->update('fuck',array('set'=>array('a'=>22,'b'=>88),'where'=>array('id','=',24))));
	/*}*/
//echo'<br>'.db::get_instance()->count_last();

//echo db::get_instance()->delete(array('tablename'=>'funk','ignore','where'=>array('id','=','9')));
//echo db::get_instance()->insert(array('tablename'=>'users','ignore','set'=>array('name'=>'Валера','age'=>date('s'))));
//echo db::get_instance()->insert(array('tablename'=>'dunk','ignore','set'=>array('id_funk'=>'9')));


?>