<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';

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

//returns 1 if session is valid, null otherwise
function checkAuth($id, $session) {
	$link = mysqli_connect(SESSIONS_DB_ADRESS, SESSIONS_DB_LOGIN, SESSIONS_DB_PASSWORD, SESSIONS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT 1 FROM SESSIONS where active = 1 and user_id = ? and session_id = ?');
	mysqli_stmt_bind_param($stmt, 'is', $id, $session);
	mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return $result;
}

function logout($id, $session) {
	$link = mysqli_connect(SESSIONS_DB_ADRESS, SESSIONS_DB_LOGIN, SESSIONS_DB_PASSWORD, SESSIONS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'UPDATE SESSIONS set active = 0 where user_id = ? and session_id = ?');
	mysqli_stmt_bind_param($stmt, 'is', $id, $session);
	mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	mysqli_stmt_close($stmt);
	mysqli_close($link);
}

function killAllSessions($id) {
	$link = mysqli_connect(SESSIONS_DB_ADRESS, SESSIONS_DB_LOGIN, SESSIONS_DB_PASSWORD, SESSIONS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'UPDATE SESSIONS set active = 0 where user_id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	mysqli_stmt_close($stmt);
	mysqli_close($link);
}

function clearSessionCookies() {
	setcookie('session', "", 1, "/");
	setcookie('id', "", 1, "/");
	setcookie('worker', "", 1, "/");
}
?>