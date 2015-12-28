<?php
	$dbname = 'cto-ro-schedule';
	$dbuser = 'root';
	$dbpassword = '';
	$dbhost = 'localhost';
	$dberrmode = PDO::ERRMODE_EXCEPTION;

	@include("db-{$_SERVER['SERVER_NAME']}.php");

	try {
		$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$db->setAttribute(PDO::ATTR_ERRMODE, $dberrmode);
	}
	catch(Exception $e) {
		die($e->getMessage());
	}
?>