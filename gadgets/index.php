<!-- list gadgets -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "List";
?>
	
<head>
	<title>List of Gadgets</title>
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
			if ($result = $mysqli -> query("SELECT * FROM gadgets")) {
				echo "<table border='1'>
				<tr>
				<th id='gadget'>Gadget Name</th>
				<th id='gadget'>ID</th>
				<th id='gadget'></th>
				<th id='gadget'></th>
				</tr>";
				
				while($row = mysqli_fetch_array($result))
				{
					$name = $row['name'];
					$gid = $row['gadget_id'];
					
					echo "<tr>";
					echo "<td><a href=view.php?id=" . $gid . ">" . $name . "</a></td>";
					echo "<td>" . $row['gadget_id'] . "</td>";
					echo "<td><a href=edit.php?id=" . $gid . ">Edit</a></td>";
					echo "<td><a href=delete.php?id=" . $gid . ">Delete</a></td>";
					echo "</tr>";
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
	
