<?php
header("Content-Type: text/html; charset=UTF-8");
$mongo=new MongoClient();
$db=$mongo->example;
$table=$db->users;
if(isset($_POST['flag']))
	{
	$document=array("name" =>$_POST["name"],"age"=>$_POST["age"]);
	$table->insert($document);
	$cursor = $table->find();
	echo'Всего в БД '.$table->count().' записей:';
	foreach ( $cursor as $id => $value )
		{
		echo "$id: ";
		var_dump($_POST);
		}
	echo'<br><a href=".">Еще</a>';
	}
else
	{
	echo'Добавить запись в БД:<br><form method="POST">Имя<input name="name" type="text" requred>Возраст<input name="age" type="text" requred><input value="1" type="hidden" name="flag"><input type="submit" value="Добавить"></form>';
	}
?>