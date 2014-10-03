<?php
require_once 'services/auth_service.php';	
require_once 'services/session_manager.php';
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
	clearSessionCookies();
	die("login failed");
}
?>