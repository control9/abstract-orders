<?php
require_once 'db_params.php';
function auth($login, $password)
{
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

function getGUID(){
	$charid = strtoupper(md5(uniqid(rand(), true)));
	$hyphen = chr(45);// "-"
	$uuid = substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12);
	return $uuid;
}

function createSession($id) {
	$session = getGUID();
	$link = mysqli_connect(SESSIONS_DB_ADRESS, SESSIONS_DB_LOGIN, SESSIONS_DB_PASSWORD, SESSIONS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'INSERT INTO SESSIONS (user_id, session_id, started, active) VALUES (?, ?, NOW(), 1)');
	mysqli_stmt_bind_param($stmt, 'is', $id, $session);
	mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return $session;
}

function checkAuth($id, $session) {
	$link = mysqli_connect(SESSIONS_DB_ADRESS, SESSIONS_DB_LOGIN, SESSIONS_DB_PASSWORD, SESSIONS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT 1 FROM SESSIONS where active = 1 and session_id = ? and user_id = ?');
	mysqli_stmt_bind_param($stmt, 'si', $session, $id);
	mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	$result =  mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return $result;
}
?>