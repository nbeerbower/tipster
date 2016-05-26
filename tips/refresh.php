<?php
	require_once("tipClass.php");
	$jsonData = tipClass::getTips(0,intval($_GET['order']));
	print $jsonData;
?>
