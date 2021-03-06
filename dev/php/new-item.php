<?php

	$executionStartTime = microtime(true);

	// Inport database info and login credentials.
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

	// Prepare query
	$query = $conn->prepare("INSERT INTO `inventory` (`id`, `name`, `stock`, `locationID`) VALUES (DEFAULT, ?, ?, ?)");
	
	$name = ucwords($_REQUEST['name']);
	$stock = $_REQUEST['stock'];
	$locId = $_REQUEST['loc'];

	// Bind query parameters
	$query->bind_param(
		"sii", 
		$name,
		$stock,
		$locId
);

	// Execute query
	$query->execute();
	
	// Fetch last inserted row
	$resultB = $conn->query('SELECT i.id, i.name, i.stock, l.name as loc, l.id as locID FROM inventory i LEFT JOIN locations l ON (l.id = i.locationID) ORDER BY i.id DESC LIMIT 1');

	$response = mysqli_fetch_assoc($resultB);

	// If insertion fails
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
	$output['data'] = $response;
	
	mysqli_close($conn);

	header('Content-Type: application/json; charset=UTF-8', true, '200');
	echo json_encode($output); 

?>
