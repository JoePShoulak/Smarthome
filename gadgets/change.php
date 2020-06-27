<!-- change gadget -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "Change";
?>

<head>
	<title>Changing Gadget</title>
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
			
			echo "Changing Gadget...<br/><br/>";
			// Perform query
			$ID = $_GET['id'];
			$name = $_POST['name'];
			
			$input_values = array();
			
			if(!empty($name)) {
				$input_values[] = "name=\"$name\"";
			}
			
			$set_values = implode(', ', $input_values);
			
			echo $set_values . "<br/>";
			
			
			echo $ID . "<br/>";
			echo $name . "<br/>";
			if ($result = $mysqli -> query("UPDATE gadgets
										SET $set_values
										WHERE gadget_id=$ID")) {
				echo("It worked!");
				header("Location: /gadgets/view.php?id=$ID");
				exit();
			} else {
				echo("Something went wrong: " . $mysqli -> error);
			}
			$result -> free_result();
			
			$mysqli -> close();
		?>
		</p>
</body>
</html>
