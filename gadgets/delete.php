<!-- delete gadget -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "Delete";
?>

<head>
	<title>Delete Gadget</title>
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
			
			$mysqli -> query("DELETE FROM gadgets WHERE gadget_id=$ID");
			$mysqli -> query("DELETE FROM modules WHERE gadget_id=$ID");
			echo "Gadget Deleted";
			
			$mysqli -> close();
		?>
	</p>
</body>
</html>
