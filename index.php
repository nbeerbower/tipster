<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Tipster</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" href="assets/ico/favicon.png">

		<!-- CSS Styling -->
		<link href="assets/tips/tips.css" rel="stylesheet">
	</head>

	<body>
		<?php
			if (isset($_GET['approve'])) {
				require_once 'credentials.php';
				$id = $_GET['approve'];
				$db_connection = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
				$db_connection->query( "SET NAMES 'UTF8'" );
				
				$statement = $db_connection->prepare("SELECT title FROM tips WHERE id=?");
				$statement->bind_param( 'i', $id );
				$statement->execute();
				$statement->bind_result( $title );
				$statement->fetch();
				$statement->close();
				
				echo "<p>".$id." ".md5($title).", |".$title."|</p>";
				if ($_GET['code'] == md5($title)) {
					$statement = $db_connection->prepare("UPDATE tips SET approved=1 WHERE id=?");
					$statement->bind_param( 'i', $id );
					$statement->execute();
					$statement->close();
					
					echo '<center><h1>"'.$title.'" has been approved!</h1></center>';
				} else {
					echo '<center><h1>There was an error with your request.</h1></center>';
				}
				
				echo "<center><a href='tips.php'>Return to Tips Section</a></center>";
			
				$db_connection->close();
			} else {
		?>
			<div class="page-header">
				<h1 style="text-align:left;float:left;">Tips</h1>
				<select id="sortSelector" style="vertical-align: bottom;float:right;">
					<option value="0">Rating</option>
					<option value="1">Newest</option>
					<option value="2">Oldest</option>
				</select>
				<hr style="clear:both;"/>
			</div>

			<div class="submitcontainer">
				<div class="submitheader"><span>Submit a Tip</span></div>
				<div style="padding: 5px;">
					<span><strong>Title</strong></span>
					<input id="title" name="title" placeholder="" class="form-control input-md" required type="text">
					<span><strong>Description</strong></span>
					<textarea class="form-control" id="description" name="description" style="resize: none; height: 200px;"></textarea>
					<span><strong>Author</strong></span>
					<input id="author" name="author" placeholder="Anonymous" class="form-control input-md" type="text">
					<input type="submit" id="submitBtn" value="Submit">
				</div>
			</div>
			<div id="tipcontainer" class="tipcontainer">
				Loading...
			</div>
			
		<?php
			}
		?>
	
	<?php
		if (!isset($_GET['approve'])) {
	?>
		<script type="text/javascript" src="assets/tips/tips.js"></script>
	<?php
		}
	?>
	</body>
</html>
