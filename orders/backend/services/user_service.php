<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';
require_once 'services/session_manager.php'; //Needed to create user session on login

function auth($login, $password){
	if (is_null($password)) {
		return null;
	}
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, pass, salt, worker from USERS where login = ? and system = 0');
	mysqli_stmt_bind_param($stmt, "s", $login);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id, $dbpass, $salt, $worker);
	$found = mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if ($found && $dbpass == hash("sha256", ($salt .$password)) ) {
		$session = createSession($id);
		return array("session" => $session, "id" => $id, "worker" => $worker);
	}
	mysqli_close($link);
	return null;
}

function checkRights($id, $worker) {
	$userdata = getUserData($id);
	if ($userdata['worker'] != $worker) {
		clearSessionCookies();
		http_response_code(403);
		die('Недостаточно прав для запрошенной операции');
	}
}


// Finds user and returns all relevant info as associative array.
function getUserData($id) {
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, login, real_name, money, worker from USERS where id = ?');
	mysqli_stmt_bind_param($stmt, "s", $id);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id, $login, $real_name, $money, $worker);
	$found = mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	if ($found) {
		return array(
			'id' => $id, 
			'login' => $login,
			'real_name' =>$real_name,
			'money' => ($money / 10000),
			'worker' => $worker
		);
	}
	else return null;
}

function getReservedId() {
	return getSystemUserId('RESERVED');
}

function getSystemId() {
	return getSystemUserId('SYSTEM');
}

function getSystemUserId($user) {
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id from USERS where login = ? ');
	mysqli_stmt_bind_param($stmt, "s", $user);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id);
	$found = mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	if ($found) {
		return $id;
	}
	else {
		http_response_code(500);
		die("Critical failure: SYSTEM account not found");
	};
}


?>