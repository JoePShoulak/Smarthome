<!-- make gadget -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "Make";
?>

<head>
	<title>Making Gadget</title>
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
			
			echo "Making Gadget...<br/><br/>";
			// Perform query
			$name = $_POST['name'];
			$voltage = $_POST['voltage'];
			if ($result = $mysqli -> query("INSERT INTO gadgets (name) VALUES ('$name')")) {
				echo("It worked!");
				$id = $mysqli->insert_id;
				header("Location: /gadgets/view.php?id=$id");
				exit();
			} else {
				echo("Something went wrong");
			}
			$result -> free_result();
			
			$mysqli -> close();
		?>
	</p>
</body>
</html>
