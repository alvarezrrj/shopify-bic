<?php

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	$executionStartTime = microtime(true);

	include("config.php");

	$conn = new mysqli($cd_host, $cd_user, $cd_password, $cd_dbname, $cd_port, $cd_socket);

	if (mysqli_connect_errno()) {
		
		$output['status']['code'] = "300";
		$output['status']['name'] = "failure";
		$output['status']['description'] = "database unavailable";
		$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
		$output['data'] = [];

		mysqli_close($conn);

		header('Content-Type: application/json; charset=UTF-8', true, '300');
		echo json_encode($output);

		exit;

	}	

	//Update employee
	$query = $conn->prepare("UPDATE `inventory` SET 
		`name`=?,
		`stock`=?,
		`locationID`=?
		WHERE id=?");
	
	$name = ucwords($_REQUEST['name']);
	$stock = $_REQUEST['stock'];
	$locId = $_REQUEST['loc'];
	$id =	$_REQUEST['id'];

	$query->bind_param(
		"siii", 
		$name,
		$stock,
		$locId,
		$id
);

	$query->execute();

	// Find edited employee and send back to render on screen
	$queryB = $conn->prepare('SELECT i.id, i.name, i.stock, l.name as loc, l.id as locID FROM inventory i LEFT JOIN locations l ON (l.id = i.locationID) WHERE i.id = ?');

	$queryB->bind_param("i", $id);

	$queryB->execute();

	$r;

	$queryB->bind_result($r['id'], $r['name'], $r['stock'], $r['loc'], $r['locID']);

	$queryB->fetch();

	//$response = mysqli_fetch_assoc($resultB);

	if (false === $query) {

		$output['status']['code'] = "400";
		$output['status']['name'] = "executed";
		$output['status']['description'] = "query failed";	
		$output['data'] = [];

		mysqli_close($conn);

		header('Content-Type: application/json; charset=UTF-8', true, '400');
		echo json_encode($output); 

		exit;

	}

	$output['status']['code'] = "200";
	$output['status']['name'] = "ok";
	$output['status']['description'] = "success";
	$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
	$output['data'] = $r;
	
	mysqli_close($conn);

	header('Content-Type: application/json; charset=UTF-8', true, '200');
	echo json_encode($output); 

?>
