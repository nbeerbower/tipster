<?php
	require_once("../credentials.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$tip_id = $_POST['tip_id'];
	
	$db_connection = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$db_connection->query( "SET NAMES 'UTF8'" );
	
	$statement = $db_connection->prepare( "SELECT count(*) FROM tip_votes WHERE tip_id=? AND ip_addr=?");
	$statement->bind_param( 'is', $tip_id, $ip );
	$statement->execute();
	$statement->bind_result( $votedAlready);
	$statement->fetch();
	$statement->close();
	
	if (!$votedAlready) {
		$statement = $db_connection->prepare( "INSERT INTO tip_votes (tip_id, ip_addr) VALUES (?, ?)");
		$statement->bind_param( 'is', $tip_id, $ip );
		$statement->execute();
		$statement->close();
	}
	
	$statement = $db_connection->prepare( "SELECT count(tip_votes.tip_id) as votes FROM tips LEFT JOIN tip_votes ON (tips.id=tip_votes.tip_id) WHERE tips.id=? GROUP BY tips.id");
	$statement->bind_param( 'i', $tip_id);
	$statement->execute();
	$statement->bind_result( $count);
	$statement->fetch();
	$statement->close();
	
	$db_connection->close();
	echo $count;
?>
