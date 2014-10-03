<?php
require_once 'config/db_params.php';
require_once 'services/session_manager.php';
	
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

$id = $_GET['id'];
$session = $_GET['session'];
$valid = checkAuth($id, $session);
if (! $valid)
{ //Removing cookies
	clearSessionCookies();
}
else {
	$data = getUserData($id);
	echo json_encode($data);
}

// Finds user and returns all relevant info as associative array.
function getUserData($id) {
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, login, real_name, money, worker, active FROM USERS where id = ?');
	mysqli_stmt_bind_param($stmt, "s", $id);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id, $login, $real_name, $money, $worker, $active);
	$found = mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	if ($found) {
		return array(
			"id" => $id, 
			"login" => $login,
			"real_name" =>$real_name,
			"money" =>$money,
			"worker" => $worker,
			"active" => $active
		);
	}
	else return null;
}

?>