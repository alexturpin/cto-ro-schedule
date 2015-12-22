<?php
	require('db.php');
	require('user.php');

	setlocale(LC_ALL, 'fr-CA');
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

			<?php
				try {
					$date = isset($_GET['date']) ? new DateTime($_GET['date']) : new DateTime();
				}
				catch(Exception $e) {
					$date = new DateTime();
				}

				echo '<h2>', mb_convert_case(utf8_encode(strftime('%B %Y', $date->getTimestamp())), MB_CASE_TITLE), '</h2>';

				$previousMonth = clone $date;
				$previousMonth->modify('first day of previous month');
				$nextMonth = clone $date;
				$nextMonth->modify('first day of next month');
			?>

			<p>
				<a href="index.php?date=<?php echo $previousMonth->format('Y-m'); ?>" class="pull-left">Mois précédent</a>
				<a href="index.php?date=<?php echo $nextMonth->format('Y-m'); ?>" class="pull-right">Mois suivant</a>
			</p>
			<table id="calendar">
				<tr>
					<th>Dimanche</th>
					<th>Lundi</th>
					<th>Mardi</th>
					<th>Mercredi</th>
					<th>Jeudi</th>
					<th>Vendredi</th>
					<th>Samedi</th>
				</tr>
				<?php
					$firstDayOfMonth = new DateTime($date->format('Y-m-01'));
					$lastDayOfMonth = new DateTime($date->format('Y-m-t'));

					$currentDay = 1;
					$daysInMonth = $lastDayOfMonth->format('j');

					while ($currentDay <= $daysInMonth) {
						echo '<tr>';
						for($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
							echo '<td><div>';

							if (!($currentDay == 1 && $dayOfWeek < $firstDayOfMonth->format('w')) && $currentDay <= $daysInMonth) {
								echo $currentDay;
								$currentDay++;
							}

							echo '</div></td>';
						}
						echo '<tr>';
					}
				?>
			</table>

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