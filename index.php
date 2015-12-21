<?php
	require('db.php');
	require('user.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>CTO RO Schedule</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="container">
			<h1>Horaire officiels de tir</h1>

			<?php if ($user) { ?>
			<p>
				Connecté en tant que <?php echo $user['name']; ?>.
				<?php if ($user['admin']) { ?><a href="admin.php">Administration</a><?php } ?>
				<a href="logout.php">Déconnexion</a>
			</p>
			<?php } else { ?>
			<p><a href="login.php">Accès officiels de tir</a></p>
			<?php } ?>
		</div>
	</body>
</html>