<?php
	require('db.php');
	require('user.php');

	setlocale(LC_ALL, 'fr-CA');

	$slots = array(
		'morning' => '9:00',
		'afternoon' => '13:00',
		'evening' => '18:00'
	);

	$stmt = $db->prepare('SELECT * FROM users ORDER BY active DESC, name ASC');
	$stmt->execute();

	$users = array();
	while($u = $stmt->fetch()) {
		$users[$u['id']] = $u;
	};

	if (isset($_POST['update-schedule']) && $user && $user['admin']) {
		$stmt = $db->prepare('INSERT INTO `schedules` (`date`, `message`, `morningOpen`, `afternoonOpen`, `eveningOpen`, `morning`, `afternoon`, `evening`) VALUES (:date, :message, :morningOpen, :afternoonOpen, :eveningOpen, :morning, :afternoon, :evening) ON DUPLICATE KEY UPDATE message = :message, morningOpen = :morningOpen, afternoonOpen = :afternoonOpen, eveningOpen = :eveningOpen, morning = :morning, afternoon = :afternoon, evening = :evening');
		$stmt->execute(array(
			'date' => $_POST['date'],
			'message' => $_POST['message'],
			'morningOpen' => isset($_POST['slot-morning-open']),
			'afternoonOpen' => isset($_POST['slot-afternoon-open']),
			'eveningOpen' => isset($_POST['slot-evening-open']),
			'morning' => isset($_POST['slot-morning-open']) ? ($_POST['slot-morning'] !== '' ? $_POST['slot-morning'] : null) : null,
			'afternoon' => isset($_POST['slot-afternoon-open']) ? ($_POST['slot-afternoon'] !== '' ? $_POST['slot-afternoon'] : null) : null,
			'evening' => isset($_POST['slot-evening-open']) ? ($_POST['slot-evening'] !== '' ? $_POST['slot-evening'] : null) : null,
		));

		$date = new DateTime($_POST['date']);
		header('Location: index.php?date=' . $date->format('Y-m'));
		exit;
	}

	if (isset($_POST['assign']) && $user && $user['active']) {
		if (isset($slots[$_POST['slot']])) {
			$stmt = $db->prepare('UPDATE schedules SET ' . $_POST['slot'] . ' = :user WHERE date = :date');
			$stmt->execute(array(
				'user' => $_POST['user'],
				'date' => $_POST['date']
			));

			$date = new DateTime($_POST['date']);
			header('Location: index.php?date=' . $date->format('Y-m'));
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
			<h1>Horaire officiels de tir</h1>

			<?php
				try {
					$date = isset($_GET['date']) ? new DateTime($_GET['date']) : new DateTime();
				}
				catch(Exception $e) {
					$date = new DateTime();
				}

				echo '<h2>', ucfirst(utf8_encode(strftime('%B %Y', $date->getTimestamp()))), '</h2>';

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
					<th><h4>Dimanche</h4></th>
					<th><h4>Lundi</h4></th>
					<th><h4>Mardi</h4></th>
					<th><h4>Mercredi</h4></th>
					<th><h4>Jeudi</h4></th>
					<th><h4>Vendredi</h4></th>
					<th><h4>Samedi</h4></th>
				</tr>
				<?php
					$firstDayOfMonth = new DateTime($date->format('Y-m-01'));
					$lastDayOfMonth = new DateTime($date->format('Y-m-t'));
					$today = new DateTime();
					$today->modify('today');

					$currentDay = 1;
					$daysInMonth = $lastDayOfMonth->format('j');

					$stmt = $db->prepare('SELECT * FROM schedules WHERE date >= :firstDayOfMonth AND date <= :lastDayOfMonth');
					$stmt->execute(array(
						'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
						'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d')
					));

					$monthSchedule = array();
					while($daySchedule = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$monthSchedule[$daySchedule['date']] = $daySchedule;
					}

					while ($currentDay <= $daysInMonth) {
						echo '<tr>';
						for($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
							$validDay = !($currentDay == 1 && $dayOfWeek < $firstDayOfMonth->format('w')) && $currentDay <= $daysInMonth;
							$currentDate = $validDay ? new DateTime($date->format('Y-m-' . $currentDay)) : null;
							$class = (!$validDay || $currentDate < $today) ? 'inactive' : ($currentDate == $today ? 'active' : '');
							echo '<td class="' . $class . '"><div class="calendar-header">';

							if ($validDay) {
								echo $currentDay++;

								$currentDateStr = $currentDate->format('Y-m-d');
								$schedule = isset($monthSchedule[$currentDateStr]) ? $monthSchedule[$currentDateStr] : array(
									'date' => $currentDateStr,
									'message' => '',
									'morningOpen' => false,
									'afternoonOpen' => false,
									'eveningOpen' => false,
									'morning' => null,
									'afternoon' => null,
									'evening' => null
								);

				?>

				<?php if ($user && $user['admin']) { ?>
								<button type="button" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#scheduleModal" data-schedule="<?php echo htmlentities(json_encode($schedule)); ?>">
									<span class="glyphicon glyphicon-pencil"></span>
								</button>
				<?php } ?>
								</div>

				<?php
								if ($schedule['message']) {
									echo '<p>', $schedule['message'], '</p>';
								}
				?>

								<div class="list-group">
								<?php
									$daysDiff = $currentDate->diff($today);
									foreach($slots as $key => $name) {
										if (!$schedule[$key . 'Open']) continue;

										$class = $schedule[$key] ? 'success' : '';
										if ($class == '' && $daysDiff->invert) {
											if ($daysDiff->d <= 1) {
												$class = 'danger';
											}
											else if ($daysDiff->d <= 7) {
												$class = 'warning';
											}
										}
										$class = 'list-group-item-' . $class;
								?>
										<a
											href=""
								<?php if ($user && $user['active'] && !$schedule[$key]) { ?>
											data-toggle="modal"
											data-target="#assignModal"
								<?php } ?>
											class="list-group-item <?php echo $class; ?>"
											data-name="<?php echo $currentDateStr, ' à ', $name; ?>"
											data-date="<?php echo $currentDateStr; ?>"
											data-slot="<?php echo $key; ?>"
											data-user="<?php echo $user['id']; ?>">
											<?php echo $name; ?>: <?php echo $schedule[$key] !== null ? $users[$schedule[$key]]['name'] : 'À combler'; ?>
										</a>
								<?php
									}
								?>
								</div>
				<?php
							}

							echo '</td>';
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

		<div class="modal" id="scheduleModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="scheduleModalTitle">Modification aux plages horaires <span></span></h4>
					</div>
					<form method="post" action="index.php">
						<div class="modal-body">
							<input type="hidden" name="date">

							<table class="table">
								<thead>
									<tr>
										<th>Plage</th>
										<th>Club ouvert</th>
										<th>Officiel de tir</th>
									</tr>
									<?php
										foreach($slots as $key => $name) {
									?>
									<tr id="slot-<?php echo $key; ?>">
										<td><?php echo $name; ?></td>
										<td><input type="checkbox" id="slot-<?php echo $key; ?>-open" name="slot-<?php echo $key; ?>-open"></td>
										<td>
											<select class="form-control" id="slot-<?php echo $key; ?>" name="slot-<?php echo $key; ?>">
												<option value="">À combler</option>
												<?php
													foreach($users as $u) {
												?>
													<option value="<?php echo $u['id']; ?>" <?php echo $u['active'] ? '' : 'disabled'; ?>><?php echo $u['name']; ?></option>
												<?php
													}
												?>
											</select>
										</td>
									</tr>
									<?php } ?>
								</thead>
							</table>

							<label for="message">Message</label>
							<textarea id="message" name="message" class="form-control" rows="3"></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
							<button type="submit" class="btn btn-primary" name="update-schedule">Enregistrer</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="modal" id="assignModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="assignModalTitle">Assignation de plage horaire <span class="slot"></span></h4>
					</div>
					<form method="post" action="index.php">
						<div class="modal-body">
							<input type="hidden" name="date">
							<input type="hidden" name="slot">
							<input type="hidden" name="user">

							<p>Souhaitez vous vraiment vous assigner la plage horaire du <span class="slot"></span>?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
							<button type="submit" class="btn btn-primary" name="assign">Assigner</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			$("button[data-target='#scheduleModal'").click(function() {
				var schedule = $(this).data("schedule");

				$("#scheduleModalTitle span").text(schedule.date);

				$("#scheduleModal input[name='date']").val(schedule.date);
				$("#scheduleModal #message").val(schedule.message);

				['morning', 'afternoon', 'evening'].forEach(function(slot) {
					$("#scheduleModal input#slot-" + slot + "-open").prop("checked", schedule[slot + "Open"] === "1").change();
					$("#scheduleModal select#slot-" + slot).val(schedule[slot] || "");
				});
			});

			$("input[id^='slot-'][type='checkbox']").change(function() {
				var select = $(this).closest("tr").find("select[id^='slot-']");
				select.prop("disabled", !$(this).prop("checked"));
				if (!$(this).prop("checked")) {
					select.val("");
				}
			});


			$("a[data-target='#assignModal'").click(function() {
				$("#assignModal span.slot").text($(this).data("name"));
				$("#assignModal input[name='date']").val($(this).data("date"));
				$("#assignModal input[name='slot']").val($(this).data("slot"));
				$("#assignModal input[name='user']").val($(this).data("user"));
			});
		</script>
	</body>
</html>