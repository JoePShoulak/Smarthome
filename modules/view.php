<!-- view module -->

<html>
	
<?php 
	$table = "Module";
	$action = "View";
?>

<head>
	<title>View Module</title>
</head>

<link rel="stylesheet" href="/style.css">

<header class="topnav">
		<a href='/gadgets/index.php'>Index</a>
		<a href='/gadgets/new.php'>New Gadget</a>
</header>

<body>
	<h1 class="content"><?php echo $table ?></h1>
	<h2 class="content"><?php echo $action ?></h2>
	
	<p class="content main">	
		
		<?php
			$mysqli = new mysqli("localhost","joe","1123","smarthome");
			
			// Check connection
			if ($mysqli -> connect_errno) {
				echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
				exit();
			}
			
			// Perform query
			
			$ID = $_GET["id"];
		
			if ($result = $mysqli -> query("SELECT * FROM modules WHERE module_id=$ID")) {
				while ($row = mysqli_fetch_array($result) ) {
									
					$name = $row['name'];
					$id = $row['module_id'];
					$gid = $row['gadget_id'];
					$data = $row['data'];
					
					if ($row['status'] == 1) {
						$status = "Pending";
					} else {
						$status = "Inactive";
					}
					
					echo "
					<a href=/gadgets/view.php?id=" . $gid . ">Back to Gadget</a><br/><br/>
					<table border='1'>
					<tr>
					<th id='module'>Module Name</th>
					<th id='module'>ID</th>
					<th id='module'>Gadget ID</th>
					<th id='module'>Status</th>
					<th id='module'>Data</th>
					<th id='module'></th>
					<th id='module'></th>
					<th id='module'></th>
					<th id='module'></th>
					</tr>
					
					<tr>
					<td>" . $name . "</td>
					<td>" . $id . "</td>
					<td>" . $gid . "</td>
					<td>" . $status . "</td>
					<td>" . $data . "</td>
					<td><a href=edit.php?id=" . $id . ">Edit</a></td>
					<td><a href=delete.php?id=" . $id . ">Delete</a></td>
					<td><a href=activate.php?id=" . $id . ">Activate</a></td>
					<td><a href=deactivate.php?id=" . $id . ">Deactivate</a></td>
					</tr>";
				
				echo "</table>";
				}
				// Free result set
				$result -> free_result();
			}	
	
			$mysqli -> close();
		?>
	</p>
</body>
</html>
