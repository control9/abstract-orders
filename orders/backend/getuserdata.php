<?php
require_once 'config/db_params.php';
require_once 'services/session_manager.php';
require_once 'services/user_service.php';
	
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

$id = $_POST['id'];
$session = $_POST['session'];
$valid = checkSession($id, $session);
if (! $valid)
{ //Removing cookies
	clearSessionCookies();
}
else {
	$data = getUserData($id);
	echo json_encode($data);
}
?>