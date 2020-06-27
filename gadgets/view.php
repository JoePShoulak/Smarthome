<!-- view gadget -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "View";
?>

<head>
	<title>View Gadget</title>
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
			if ($result = $mysqli -> query("SELECT * FROM gadgets WHERE gadget_id=$ID")) {
				while ($row = mysqli_fetch_array($result) ) {
				echo "<table border='1'>
				<tr>
				<th id='gadget'>Gadget Name</th>
				<th id='gadget'>ID</th>
				<th id='gadget'></th>
				<th id='gadget'></th>
				<th id='gadget'></th>
				</tr>";
				
					$name = $row['name'];
					$gid = $row['gadget_id'];
					
					echo "<tr>";
					echo "<td><a href=view.php?id=" . $gid . ">" . $name . "</a></td>";
					echo "<td>" . $gid . "</td>";
					echo "<td><a href=/modules/new.php?id=" . $gid . ">Add Module</a></td>";
					echo "<td><a href=edit.php?id=" . $gid . ">Edit</a></td>";
					echo "<td><a href=delete.php?id=" . $gid . ">Delete</a></td>";
					echo "</tr>";
				
				echo "</table>";
				}
				// Free result set
				$result -> free_result();
			}	
			if ($result = $mysqli -> query("SELECT * FROM modules WHERE gadget_id=$ID")) {
				echo "
					<table border='1'>
					<tr>
					<th id='module'>Module Name</th>
					<th id='module'>ID</th>
					<th id='module'>Status</th>
					<th id='module'>Data</th>
					<th id='module'></th>
					<th id='module'></th>
					<th id='module'></th>
					<th id='module'></th>
					</tr>";
					
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
					<tr>
					<td>" . $name . "</td>
					<td>" . $id . "</td>
					<td>" . $status . "</td>
					<td>" . $data . "</td>
					<td><a href=/modules/edit.php?id=" . $id . ">Edit</a></td>
					<td><a href=/modules/delete.php?id=" . $id . ">Delete</a></td>
					<td><a href=/modules/activate.php?id=" . $id . ">Activate</a></td>
					<td><a href=/modules/deactivate.php?id=" . $id . ">Deactivate</a></td>
					</tr>";
				
				}
				echo "</table>";

				// Free result set
				$result -> free_result();
			}	
			$mysqli -> close();
		?>
	</p>
</body>
</html>
