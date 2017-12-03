<?php
	require('vendor/autoload.php');
	require('db.php');
	require('user.php');

	$vCalendar = new \Eluceo\iCal\Component\Calendar('http://alexturpin.net');
	$vCalendar->setName('Officiels CTO');

	$date = new DateTime();
	$firstDayOfMonth = new DateTime($date->format('Y-m-01'));
	$stmt = $db->prepare('SELECT * FROM schedules WHERE date >= :firstDayOfMonth');
	$stmt->execute(array(
		'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d')
	));

	while($daySchedule = $stmt->fetch(PDO::FETCH_ASSOC)) {
		foreach($slots as $key => $name) {
			if (!$daySchedule[$key . 'Open']) continue;

			$vEvent = new \Eluceo\iCal\Component\Event();
			$vEvent
				->setDtStart(new \DateTime($daySchedule['date']))
				->setDtEnd(new \DateTime($daySchedule['date']))
				->setNoTime(true)
				->setSummary(('Officiel ' . $name . ': ') . ($daySchedule[$key] !== null ? $users[$daySchedule[$key]]['name'] : 'À combler'));

			$vCalendar->addComponent($vEvent);
		}
	}

	header('Content-Type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename="cal.ics"');

	echo $vCalendar->render();
?>