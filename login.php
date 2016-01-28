<?php
	require('db.php');
	session_start();

	$error = '';
	if (isset($_POST['email']) && $_POST['email']) {
		$stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
		$stmt->execute(array(
			'email' => $_POST['email']
		));

		$user = $stmt->fetch();

		if (!$user) {
			$error = 'Adresse courriel non reconnue. Veuillez vérifier l\'ortographe et contacter un administrateur si le problème persiste.';
		}
		else {
			if ($user['password'] == '') {
				header('Location: firstLogin.php?email=' . $user['email']);
				exit;
			}
			else if (!$user['active']) {
				$error = 'Votre compte a été désactivé. Veuillez contacter un administrateur.';
			}
			else {
				if (password_verify($_POST['password'], $user['password'])) {
					$_SESSION['user'] = $user['id'];
					header('Location: index.php');
					exit;
				}
				else {
					$error = 'Mot de passe invalide.';
				}
			}
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
			<h1>Connexion</h1>
			<?php if (isset($_GET['firstLoginDone'])) { ?>
			<div class="alert alert-success">Votre mot de passe a été créé, vous pouvez maintenant vous connecter.</div>
			<?php } ?>
			<?php if ($error) { ?>
			<div class="alert alert-danger"><?php echo $error; ?></div>
			<?php } ?>
			<form class="form-horizontal" method="post" action="login.php">
				<div class="form-group">
					<label for="email" class="col-sm-2 control-label">Adresse courriel</label>
					<div class="col-sm-10">
						<input type="email" class="form-control" id="email" name="email" placeholder="Adresse courriel" value="<?php echo isset($_GET['firstLoginDone']) ? $_GET['firstLoginDone'] : '';  ?>" required>
					</div>
				</div>
				<div class="form-group">
					<label for="password" class="col-sm-2 control-label">Mot de passe</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Connexion</button>
					</div>
				</div>
			</form>

			<p><a href="firstLogin.php">Première connexion?</a></p>
			<p><a href="index.php">Retour au calendrier</a></p>
		</div>
	</body>
</html>