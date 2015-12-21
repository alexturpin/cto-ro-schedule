<?php
	$dbname = 'cto-ro-schedule';
	$dbuser = 'root';
	$dbpassword = '';
	$dbhost = 'localhost';
	$dberrmode = PDO::ERRMODE_EXCEPTION;

	if ($_SERVER['SERVER_NAME'] == 'alexturpin.net') {
		$dbname = '';
		$dbuser = '';
		$dbpassword = '';
		$dbhost = '';
		$dberrmode = PDO::ERRMODE_SILENT;
	}

	try {
		$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$db->setAttribute(PDO::ATTR_ERRMODE, $dberrmode);
	}
	catch(Exception $e) {
		die($e->getMessage());
	}
?>