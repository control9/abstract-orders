<?php
require_once 'services/session_manager.php';
	
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

$id = $_POST['id'];
if ($id) {
	$session = $_POST['session'];
	if ($session) {
		logout($id, $session);
	}
	else {
		killAllSessions($id);
	}
}
clearSessionCookies();
?>
