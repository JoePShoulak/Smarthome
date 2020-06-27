<html>
<?php
	$mysqli = new mysqli("localhost","joe","1123","smarthome");
			
	// Check connection
	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}
	
	// Perform query
	$ID = $_GET['id'];
	$data = $_GET['data'];
	
	if ($result = $mysqli -> query("UPDATE modules
								SET data=" . $data . "
								WHERE module_id=$ID")) {
		header("Location: /modules/view.php?id=$ID");
		exit();
	} else {
		echo("Something went wrong: " . $mysqli -> error);
	}
	$result -> free_result();
	
	$mysqli -> close();
?>
</html>
