<?php
	require('db.php');
	require('user.php');

	if (!$user || !$user['admin']) {
		header('Location: index.php');
		exit;
	}
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
			<h1>Administration</h1>

			<h2>Officiels de tir</h2>
			<table class="table table-stripped">
				<thead>
					<tr>
						<th>Nom</th>
						<th>Courriel</th>
						<th>Statut</th>
						<th>Réinitialiser le mot de passe</th>
						<th>Activer / Désactiver</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$stmt = $db->prepare('SELECT * FROM users ORDER BY admin DESC, active DESC');
						$stmt->execute();

						while($user = $stmt->fetch()) {
					?>
						<tr>
							<td><?php echo $user['name']; ?></td>
							<td><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
							<td><?php echo $user['admin'] ? 'Administrateur' : ($user['active'] ? 'Actif' : 'Désactivé'); ?></td>
							<td><?php if ($user['password'] != '') { ?><form><button type="submit" class="btn btn-default">Réinitialiser le mot de passe</button></form><?php } else { echo '-'; } ?></td>
							<td><?php if (!$user['admin']) { ?><form><button type="submit" class="btn btn-default"><?php echo $user['active'] ? 'Désactiver' : 'Activer'; ?></button></form><?php } else { echo '-'; } ?></td>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>

			<h2>Nouvel officiel de tir</h2>
			<form class="form-horizontal" method="post" action="firstLogin.php">
				<div class="form-group <?php if (isset($errors['email'])) { echo 'has-error'; } ?>">
					<label for="email" class="col-sm-2 control-label">Adresse courriel</label>
					<div class="col-sm-10">
						<input type="email" class="form-control" id="email" name="email" placeholder="Adresse courriel" value="<?php echo isset($_POST['email']) ? $_POST['email'] : (isset($_GET['email']) ? $_GET['email'] : ''); ?>" required>
						<?php if (isset($errors['email'])) { ?><span class="help-block"><?php echo $errors['email']; ?></span><?php } ?>
					</div>
				</div>
				<div class="form-group <?php if (isset($errors['name'])) { echo 'has-error'; } ?>">
					<label for="name" class="col-sm-2 control-label">Nom</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
						<?php if (isset($errors['name'])) { ?><span class="help-block"><?php echo $errors['name']; ?></span><?php } ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Créer</button>
					</div>
				</div>
			</form>


			<p><a href="index.php">Retour au calendrier</a></p>
		</div>
	</body>
</html>