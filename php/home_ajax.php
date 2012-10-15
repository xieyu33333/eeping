<?php
require '../php/conn.php';
if(isset($_GET['par_id'])){
	$sql='SELECT`id`,`title`,`describe`,`link`FROM`ceedb`.`product_list`WHERE`par_id`=\''.$_GET['par_id'].'\'';
	$result=mysql_query($sql);
	$r=array();
	while($row=mysql_fetch_assoc($result)){
	$arrin=array('title'=>$row['title'],'describe'=>$row['describe'],'link'=>$row['link']);
	$arr=array($row['id']=>$arrin);
	$r=array_merge($r,$arr);
	}
	echo json_encode($r);
}else{
	$sql='SELECT`par_id`FROM`ceedb`.`product_list`WHERE`id`=\''.$_GET['id'].'\'';
	$result=mysql_query($sql);
	$r=mysql_fetch_row($result);
	echo json_encode($r);
}
?>