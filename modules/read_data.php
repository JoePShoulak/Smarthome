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
	
	if ($result = $mysqli -> query("SELECT * FROM modules WHERE module_id=$ID")) {

		while ($row = mysqli_fetch_array($result) ) {
										
			$status = $row['data'];
			
			echo $status;
		}
	}
	
	$result -> free_result();
	
	$mysqli -> close();
?>
</html>
