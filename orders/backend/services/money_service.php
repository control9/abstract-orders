<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';

function transferMoney($from, $to, $amount, $context) {
	if ($amount <= 0) {
		return 'negative amount';
	}
	$transferId = logIncompleteTransfer($from, $to, $amount, $context);
	if ($transferId <= 0) {
		return 'transfer init failed';
	}
	$transferResult = doTransfer($from, $to, $amount, $context);
	if (!$transferResult) {
		return 'transfer failed';
	}
	if (!completeTransfer($transferId)) {
		return 'transfer incomplete';
	}
	return 'ok';
}

function logIncompleteTransfer($from, $to, $amount, $context) {
	$link = mysqli_connect(TRANSFERS_DB_ADRESS, TRANSFERS_DB_LOGIN, TRANSFERS_DB_PASSWORD, TRANSFERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'INSERT INTO TRANSFERS (from_id, to_id, amount, context_id, completed) VALUES (?, ?, ?, ?, 0)');
	mysqli_stmt_bind_param($stmt, 'iiii', $from, $to, $amount, $context);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$insertId = mysqli_insert_id($link);
	mysqli_close($link);
	return $insertId;
}

function doTransfer($from, $to, $amount, $context) {
	$link = mysqli_connect(USERS_DB_ADRESS, USERS_DB_LOGIN, USERS_DB_PASSWORD, USERS_DB_NAME);
	mysqli_begin_transaction($link);
	$stmt = mysqli_prepare($link, 'SELECT money from USERS where id = ? FOR UPDATE');
	$id = $from;
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $from_money);
	mysqli_stmt_fetch($stmt);
	$id = $to;
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $to_money);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if ($from_money < $amount) {
		mysqli_rollback($link);
		return false;
	}
	$from_money = $from_money - $amount;
	$to_money = $to_money + $amount;
	$stmt = mysqli_prepare($link, 'UPDATE USERS set money = ? where id = ?');
	mysqli_stmt_bind_param($stmt, 'ii', $from_money, $from);
	if (!mysqli_stmt_execute($stmt)) {
		mysqli_rollback($link);
		return false;
	}
	mysqli_stmt_bind_param($stmt, 'ii', $to_money, $to);
	if (!mysqli_stmt_execute($stmt)) {
		mysqli_rollback($link);
		return false;
	}
	mysqli_stmt_close($stmt);
	mysqli_commit($link);
	mysqli_close($link);
	return true;
}

function completeTransfer($transferId) {
	$link = mysqli_connect(TRANSFERS_DB_ADRESS, TRANSFERS_DB_LOGIN, TRANSFERS_DB_PASSWORD, TRANSFERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'UPDATE TRANSFERS set completed = true where id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $transferId);
	$result = mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return $result;
}

?>