<?php
include 'doauth.php';
// Prevent caching.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

// The JSON standard MIME header.
header('Content-type: application/json');

$count = $_GET['count'];
$data = array();
for ($i = 0; $i < $count; $i = $i + 1) {
	$id = $i;
	$content = $id . '-content';
	$data[] = array( 'id' => $id, 'content' => $content);
}
echo json_encode($data);

?>
