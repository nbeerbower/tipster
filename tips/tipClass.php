<?php
/* tipClass
 * provides primary functionality for Tips section
 * 
 * Nicholas Beerbower 2016
 */
require_once("../credentials.php");
class tipClass {
	public static function getTips($limit, $order) {
		// order = 0 by most votes
		// order = 1 by newest
		// order = 2 by oldest
		switch ($order) {
			case 0:
				$orderStr = "votes DESC";
				break;
			case 1:
				$orderStr = "submit_time DESC";
				break;
			case 2:
				$orderStr = "submit_time ASC";
				break;
		}
		// TODO: Implement limit and order
		$arr = array();
		$jsonData = '{"results":[';
		$db_connection = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$db_connection->query( "SET NAMES 'UTF8'" );
		// TODO: nested query to get # of votes by count
		//$statement = $db_connection->prepare( "SELECT id, title, description, author, submit_time FROM tips WHERE approved=1 ORDER BY ". $orderStr ." LIMIT 100");
		$statement = $db_connection->prepare( "SELECT tips.id, title, description, author, submit_time, count(tip_votes.tip_id) as votes FROM tips LEFT JOIN tip_votes ON (tips.id=tip_votes.tip_id) WHERE approved=1 GROUP BY tips.id ORDER BY ". $orderStr ." LIMIT 100");
		$statement->execute();
		$statement->bind_result( $id, $title, $description, $author, $submit_time, $votes );
		$line = new stdClass;
		while ($statement->fetch()) {
			$line->id = $id;
			$line->title = $title;
			$line->description = $description;
			$line->author = $author;
			$line->submit_time = date('m/d/o H:i', strtotime($submit_time));
			$line->votes = $votes;
			$arr[] = json_encode($line);
		}
		$statement->close();
		$db_connection->close();
		$jsonData .= implode(",", $arr);
		$jsonData .= ']}';
		return $jsonData;
	}

	public static function sendTip( $title, $description, $author ) {
		if ( $title != "" && $description != "" ) {
			if ($author == "") $author = "Anonymous";
			$db_connection = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
			$db_connection->query( "SET NAMES 'UTF8'" );
			$statement = $db_connection->prepare( "INSERT INTO tips( title, description, author ) VALUES(?, ?, ?)");
			$statement->bind_param( 'sss', $title, $description, $author );
			$statement->execute();
			$statement->close();
			
			$statement = $db_connection->prepare( "SELECT id FROM tips WHERE title=? AND description=? AND author=?");
			$statement->bind_param( 'sss', $title, $description, $author );
			$statement->execute();
			$statement->bind_result( $id );
			$statement->fetch();
			$statement->close();
			
			$emailQuery = "SELECT id, email FROM administrative_users;"; 
			$emailResult = $db_connection->query($emailQuery);
			
			$subject = "Centre County MHID Tip Submission Request";
			$link = "http://" . CURRENT_DOMAIN . "/tips.php?approve=" . $id . "&code=" . md5($title);
			$message = "<span><strong>Title</strong></span><p>".$title."</p><span><strong>Description</strong></span><p>".$description."</p><span><strong>Author</strong></span><p>".$author."</p></br><a href='".$link."'>Click here to approve this tip!</a>";
			$headers = "From: noreply@" . CURRENT_DOMAIN . "\r\n" . "X-Mailer: PHP/" . phpversion() . "\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";

			while($row = $emailResult->fetch_assoc()) {
				mail($row['email'], $subject, $message, $headers);
			}
			
			$db_connection->close();
		}
	}
}
?>
