<?php
	require('db.php');
	require('user.php');

	if (!$user || !$user['admin']) {
		header('Location: index.php');
		exit;
	}

	$errors = array();
	if (isset($_POST['new-user'])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errors['email'] = 'Adresse courriel invalide.';
		}

		if (strlen($_POST['name']) < 3) {
			$errors['name'] = 'Le nom doit contenir au moins 3 caractères.';
		}

		if (count($errors) == 0) {
			$stmt = $db->prepare('INSERT INTO `users` (`id`, `email`, `password`, `name`, `active`, `admin`) VALUES (NULL, :email, \'\', :name, true, false)');
			$stmt->execute(array(
				'email' => $_POST['email'],
				'name' => $_POST['name']
			));

			header('Location: admin.php');
			exit;
		}
	}

	if (isset($_POST['reset-password'])) {
		$stmt = $db->prepare('UPDATE users SET password = \'\' WHERE id = :id');
		$stmt->execute(array(
			'id' => $_POST['user']
		));

		header('Location: admin.php');
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

						while($u = $stmt->fetch()) {
					?>
						<tr>
							<td><?php echo $u['name']; ?></td>
							<td><a href="mailto:<?php echo $u['email']; ?>"><?php echo $u['email']; ?></a></td>
							<td><?php echo $u['admin'] ? 'Administrateur' : ($u['active'] ? 'Actif' : 'Désactivé'); ?></td>
							<td>
								<?php if ($u['password'] != '' && $u['id'] != $user['id']) { ?>
								<form method="post" action="admin.php">
									<input type="hidden" name="user" value="<?php echo $u['id']; ?>">
									<button type="submit" class="btn btn-default" name="reset-password">Réinitialiser le mot de passe</button>
								</form>
								<?php } else { echo '-'; } ?>
							</td>
							<td>
								<?php if (!$u['admin']) { ?>
								<form>
									<input type="hidden" name="user" value="<?php echo $u['id']; ?>">
									<button type="submit" class="btn btn-default"><?php echo $u['active'] ? 'Désactiver' : 'Activer'; ?></button>
								</form>
								<?php } else { echo '-'; } ?>
							</td>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>

			<h2>Nouvel officiel de tir</h2>
			<form class="form-horizontal" method="post" action="admin.php">
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
						<button type="submit" class="btn btn-default" name="new-user">Créer</button>
					</div>
				</div>
			</form>


			<p><a href="index.php">Retour au calendrier</a></p>
		</div>
	</body>
</html>