<?php
require_once 'doauth.php';	
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

$login = $_POST['login'];
$password = $_POST['password'];
$remember = $_POST['remember'];

if ($remember) {
	$expire = 365 * 24 * 3600 + time();
}
else $expire = 0;

$userdata = auth($login, $password);
if ($userdata) {
	setcookie('session', $userdata['session'], $expire, "/");
	setcookie('id', $userdata['id'], $expire, "/");
	setcookie('worker', $userdata['worker'], $expire, "/");
}
else { //Removing cookies
	setcookie('session', "", 1, "/");
	setcookie('id', "", 1, "/");
	setcookie('worker', "", 1, "/");
	die("login failed");
}
?>