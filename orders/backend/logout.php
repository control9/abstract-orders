<?php
require_once 'services/session_manager.php';
	
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

$id = $_POST['id'];
$session = $_POST['session'];
$all = $_POST['all'];

$valid = checkSession($id, $session);
if ($valid) {
	if ($all) {
		logout($id, $session);
	}
	else {
		killAllSessions($id);
	}
}
clearSessionCookies();
?>
