<?php
require_once 'db_params.php';
function auth($login, $password)
{
	if (is_null($password)) {
		return null;
	}
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, pass, worker FROM USERS where login = ?');
	mysqli_stmt_bind_param($stmt, "s", $login);
	$result =  mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
	if (!$result) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return null;
	}
	mysqli_stmt_bind_result($stmt, $id, $dbpass, $worker);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if ($dbpass == $password) {
		$session = getGUID();
		$stmt = mysqli_prepare($link, 'UPDATE USERS set session  = ? where id = ?');
		mysqli_stmt_bind_param($stmt, 'ss', $session, $id);
		mysqli_stmt_execute($stmt) or die('request failed: ' . mysqli_error($link));
		mysqli_stmt_close($stmt);
		mysqli_close($link);
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
?>