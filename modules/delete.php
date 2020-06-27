<!-- delete module -->

<html>
	
<?php 
	$table = "Module";
	$action = "Delete";
?>

<head>
	<title>Delete Module</title>
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
			$result = $mysqli -> query("SELECT * FROM modules WHERE module_id=$ID");
			$GID = mysqli_fetch_array($result)['gadget_id'];
			$mysqli -> query("DELETE FROM modules WHERE module_id=$ID");
			echo "Module Deleted<br/>";
			echo ("<a href=/gadgets/view.php?id=$GID>Back to Gadget</a>");
	
			
			$mysqli -> close();
		?>
	</p>
</body>
</html>
