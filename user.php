<?php
	session_start();

	$user = false;
	if (isset($_SESSION['user'])) {
		$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
		$stmt->execute(array(
			'id' => $_SESSION['user']
		));

		if ($stmt->rowCount() == 1) {
			$user = $stmt->fetch();
		}
	}

	$stmt = $db->prepare('SELECT * FROM users ORDER BY active DESC, name ASC');
	$stmt->execute();

	$users = array();
	while($u = $stmt->fetch()) {
		$users[$u['id']] = $u;
	};
?>