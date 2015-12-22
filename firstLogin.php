<?php
	require('db.php');

	$errors = array();

	if (isset($_POST['email'])) {
		$stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
		$stmt->execute(array(
			'email' => $_POST['email']
		));

		$user = $stmt->fetch();

		if (!$user) {
			$errors['email'] = 'Adresse courriel non reconnue. Veuillez parler à un administrateur.';
		}

		if (isset($user['password']) && $user['password'] != '') {
			$errors['firstLoginDone'] = 'Cet utilisateur possède déjà un mot de passe. Souhaitez vous plutôt vous <a href="login.php">connecter?</a> Si vous avez oublié votre mot de passe, vous devrez parler à un administrateur.';
		}

		if (mb_strlen($_POST['password']) < 4) {
			$errors['password'] = 'Le mot de passe doit contenir au moins 4 caractères.';
		}

		if ($_POST['password'] != $_POST['passwordConfirm']) {
			$errors['passwordConfirm'] = 'Les deux mots de passes doivent être identique.';
		}

		if (count($errors) == 0) {
			$stmt = $db->prepare('UPDATE users SET password = :password WHERE email = :email');
			$stmt->execute(array(
				'email' => $_POST['email'],
				'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
			));

			header('Location: login.php?firstLoginDone=' . $user['email']);
			exit;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>CTO RO Schedule</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">

		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</head>
	<body>
		<div class="container">
			<h1>Première connexion</h1>

			<p>Puisque c'est votre première connexion, nous devons créer votre mot de passe.</p>

			<?php if (isset($errors['firstLoginDone'])) { ?>
			<div class="alert alert-danger" role="alert"><?php echo $errors['firstLoginDone']; ?></div>
			<?php } ?>
			<form class="form-horizontal" method="post" action="firstLogin.php">
				<div class="form-group <?php if (isset($errors['email'])) { echo 'has-error'; } ?>">
					<label for="email" class="col-sm-2 control-label">Adresse courriel</label>
					<div class="col-sm-10">
						<input type="email" class="form-control" id="email" name="email" placeholder="Adresse courriel" value="<?php echo isset($_POST['email']) ? $_POST['email'] : (isset($_GET['email']) ? $_GET['email'] : ''); ?>" required>
						<?php if (isset($errors['email'])) { ?><span class="help-block"><?php echo $errors['email']; ?></span><?php } ?>
					</div>
				</div>
				<div class="form-group <?php if (isset($errors['password'])) { echo 'has-error'; } ?>">
					<label for="password" class="col-sm-2 control-label">Mot de passe désiré</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" required>
						<?php if (isset($errors['password'])) { ?><span class="help-block"><?php echo $errors['password']; ?></span><?php } ?>
					</div>
				</div>
				<div class="form-group <?php if (isset($errors['passwordConfirm'])) { echo 'has-error'; } ?>">
					<label for="passwordConfirm" class="col-sm-2 control-label">Confirmer le mot de passe</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm" placeholder="Mot de passe" value="<?php echo isset($_POST['passwordConfirm']) ? $_POST['passwordConfirm'] : ''; ?>" required>
						<?php if (isset($errors['passwordConfirm'])) { ?><span class="help-block"><?php echo $errors['passwordConfirm']; ?></span><?php } ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Créer mot de passe</button>
					</div>
				</div>
			</form>

			<p><a href="index.php">Retour au calendrier</a></p>
		</div>
	</body>
</html>