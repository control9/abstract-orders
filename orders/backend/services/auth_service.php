<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';
require_once 'services/session_manager.php';

function auth($login, $password){
	if (is_null($password)) {
		return null;
	}
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, pass, worker FROM USERS where login = ? and active = 1');
	mysqli_stmt_bind_param($stmt, "s", $login);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id, $dbpass, $worker);
	$found = mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if ($found && $dbpass == $password) {
		$session = createSession($id);
		return array("session" => $session, "id" => $id, "worker" => $worker);
	}
	mysqli_close($link);
	return null;
}
?>