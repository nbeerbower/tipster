<?php
	require_once("tipClass.php");
	$title = strip_tags($_POST['title']);
	$description = strip_tags($_POST['description']);
	$author = strip_tags($_POST['author']);
	tipClass::sendTip($title, $description, $author);
?>
