<!-- new gadget -->

<html>
	
<?php 
	$table = "Gadgets";
	$action = "New";
?>

<head>
	<title>New Gadget</title>
</head>

<link rel="stylesheet" href="/style.css">

<header class="topnav">
		<a href='/gadgets/index.php'>Index</a>
		<a href='/gadgets/new.php'>New Gadget</a>
</header>

<body>
	<h1 class="content"><?php echo $table ?></h1>
	<h2 class="content"><?php echo $action ?></h2>
		
	<form class="content main" action="make.php" method="post">
		Name: <input type="text" name="name"><br>
		<input type="submit">
	</form>
</body>

</html>
