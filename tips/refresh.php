<?php
	require_once("tipClass.php");
	$jsonData = tipClass::getTips(0,$_GET['order']);
	print $jsonData;
?>
